<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Comment;
use App\Services\Logging\ActionLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller responsible for comments on ideas.
 *
 * NOTE:
 * - No validation (TODO)
 * - No limit per user (add max 3 comments per idea) (TODO)
 * - No authorization on delete (TODO secure)
 */
class CommentController extends Controller
{
    public function __construct(
        private readonly ActionLogService $actionLog
    ) {}

    /**
     * Store a new comment for an idea.
     */
    public function store(Request $request, Idea $idea)
    {
        $comment = Comment::create([
            'idea_id'     => $idea->id,
            'user_id'     => Auth::id(),
            'description' => $request->input('description'), // XSS vulnerable
        ]);

        $this->actionLog->log(
            userId: Auth::id(),
            action: 'comment_created',
            ideaId: $idea->id,
            commentId: $comment->id,
            dataAfter: json_encode(['description' => $comment->description]),
            request: $request,
        );

        return redirect()
            ->route('ideas.show', $idea)
            ->with('status', 'Comment added.');
    }

    public function update(Request $request, Idea $idea, Comment $comment)
    {
        $this->authorize('update', $comment);

        $before = $comment->only(['description']);

        $comment->update([
            'description' => $request->input('description'), // XSS vulnerable
        ]);

        $this->actionLog->log(
            userId: Auth::id(),
            action: 'comment_updated',
            ideaId: $idea->id,
            commentId: $comment->id,
            dataBefore: json_encode($before),
            dataAfter: json_encode(['description' => $comment->description]),
            request: $request,
        );

        return redirect()
            ->route('ideas.show', $idea)
            ->with('status', 'Comment updated.');
    }

    /**
     * Remove a comment.
     *
     * NOTE:
     * - No authorization check ANY user can delete ANY comment (TODO)
     */
    public function destroy(Idea $idea, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $before     = $comment->only(['description']);
        $commentId  = $comment->id;

        $comment->delete();

        $this->actionLog->log(
            userId: Auth::id(),
            action: 'comment_deleted',
            ideaId: $idea->id,
            commentId: $commentId,
            dataBefore: json_encode($before),
            request: request(),
        );

        return redirect()
            ->route('ideas.show', $idea)
            ->with('status', 'Comment deleted.');
    }
}