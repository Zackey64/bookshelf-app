<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReadingPlanController extends Controller
{
    // 読書計画一覧画面
    public function index(): View
    {
        $query = auth()->user()->readingPlans()->with('book');
        if (request('status')) {
            $query->where('status', request('status'));
        }
        $readingPlans = $query->latest()->get();

        return view('reading-plans.index', [
            'readingPlans' => $readingPlans,
            'currentStatus' => request('status'),
        ]);
    }

    // 読書計画登録画面
    public function create(): View
    {
        $books = Book::orderBy('title')->get();

        return view('reading-plans.create', compact('books'));
    }

    // 読書計画登録処理
    public function store(StoreReadingPlanRequest $request): RedirectResponse
    {
        $request->user()->readingPlans()->create([
            'book_id' => $request->validated('book_id'),
            'target_date' => $request->validated('target_date'),
            'status' => ReadingPlanStatus::InProgress,
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を登録しました。');
    }

    // 読書計画編集画面
    public function edit(ReadingPlan $readingPlan): View
    {
        $this->authorize('update', $readingPlan);
        $readingPlan->load('book');

        return view('reading-plans.edit', compact('readingPlan'));
    }

    // 読書計画編集処理
    public function update(UpdateReadingPlanRequest $request, ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('update', $readingPlan);
        $readingPlan->update($request->validated());

        return redirect()->route('reading-plans.index')->with('success', '読書計画を更新しました。');
    }

    // 読書計画削除処理
    public function destroy(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('delete', $readingPlan);
        $readingPlan->delete();

        return redirect()->route('reading-plans.index')->with('success', '読書計画を削除しました。');
    }

    // 読書計画読了
    public function complete(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('complete', $readingPlan);
        $readingPlan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を完了しました。');
    }
}
