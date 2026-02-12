<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\Services\ResumeCommandService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AdminResumeActionController extends Controller
{
    public function __construct(private ResumeCommandService $commands)
    {
    }

    public function update(int $id, Request $request): RedirectResponse
    {
        $this->authorize('admin.resumes.update');

        $data = $request->validate([
            'status' => ['required', 'string', 'in:draft,published,archived'],
        ]);

        $resume = $this->commands->patch($id, null, null, $data['status']);

        if ($resume === null) {
            return redirect()->route('admin.resumes.index')
                ->with('error', 'Resume not found.');
        }

        return redirect()->route('admin.resumes.show', $id)
            ->with('status', 'Status updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorize('admin.resumes.delete');

        $resume = $this->commands->delete($id);

        if ($resume === null) {
            return redirect()->route('admin.resumes.index')
                ->with('error', 'Resume not found.');
        }

        return redirect()->route('admin.resumes.index')
            ->with('status', 'Resume deleted.');
    }
}
