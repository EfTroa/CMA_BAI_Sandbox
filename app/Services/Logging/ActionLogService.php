<?php

namespace App\Services\Logging;

use App\Models\ActionLog;
use Illuminate\Http\Request;

/**
 * Service responsible for logging security-relevant actions.
 *
 * TODO later:
 *  - Add filtering in logs page
 *  - Add retention rules (purge)
 */
class ActionLogService
{
    /**
     * Catégories d'actions connues → utilisées pour déduire la category automatiquement.
     */
    private const CATEGORY_MAP = [
        'auth'    => ['login', 'logout', 'register', 'password'],
        'admin'   => ['admin', 'purge', 'ban'],
        'profile' => ['profile', 'email', 'avatar'],
        'security'=> ['forbidden', 'bruteforce', 'injection', 'xss'],
    ];

    /**
     * Actions qui indiquent un avertissement ou un incident.
     */
    private const WARNING_ACTIONS  = ['login_failed', 'forbidden', 'password_reset'];
    private const CRITICAL_ACTIONS = ['bruteforce', 'injection', 'xss', 'admin_delete'];

    public function log(
        ?int $userId,
        string $action,
        ?int $ideaId = null,
        ?int $commentId = null,
        ?string $dataBefore = null,
        ?string $dataAfter = null,
        ?Request $request = null,
        string $result = 'success',
        ?int $httpStatus = null,
    ): void {
        $severity = $this->resolveSeverity($action, $result);
        $category = $this->resolveCategory($action);
        $checksum = $this->computeChecksum($userId, $action, $dataBefore, $dataAfter);

        ActionLog::create([
            'user_id'     => $userId,
            'action'      => $action,
            'idea_id'     => $ideaId,
            'comment_id'  => $commentId,
            'data_before' => $dataBefore,
            'data_after'  => $dataAfter,
            'ip_address'  => $request?->ip(),
            'user_agent'  => $request?->userAgent(),
            'session_id'  => $request?->session()?->getId(),
            'severity'    => $severity,
            'result'      => $result,
            'category'    => $category,
            'http_status' => $httpStatus,
            'checksum'    => $checksum,
        ]);
    }

    /**
     * Détermine la gravité selon l'action et le résultat.
     *
     * Règle :
     *  - failure sur une action critique → critical
     *  - action dans la liste warning   → warning
     *  - tout le reste                  → info
     */
    private function resolveSeverity(string $action, string $result): string
    {
        foreach (self::CRITICAL_ACTIONS as $keyword) {
            if (str_contains($action, $keyword)) {
                return 'critical';
            }
        }

        if ($result === 'failure' || $result === 'error') {
            return 'warning';
        }

        foreach (self::WARNING_ACTIONS as $keyword) {
            if (str_contains($action, $keyword)) {
                return 'warning';
            }
        }

        return 'info';
    }

    /**
     * Déduit la catégorie à partir du nom de l'action.
     */
    private function resolveCategory(string $action): string
    {
        foreach (self::CATEGORY_MAP as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($action, $keyword)) {
                    return $category;
                }
            }
        }

        return 'content';
    }

    /**
     * Calcule un hash SHA-256 du log pour détecter toute falsification ultérieure.
     *
     * Le checksum couvre : user_id + action + data_before + data_after.
     */
    private function computeChecksum(?int $userId, string $action, ?string $dataBefore, ?string $dataAfter): string
    {
        $raw = implode('|', [
            (string) $userId,
            $action,
            (string) $dataBefore,
            (string) $dataAfter,
        ]);

        return hash('sha256', $raw);
    }
}