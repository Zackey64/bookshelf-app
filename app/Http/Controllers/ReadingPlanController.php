<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * 読書計画コントローラー
 */
class ReadingPlanController extends Controller
{
    /**
     * 読書計画の一覧を表示
     */
    public function index(): View
    {
        $query = auth()->user()->readingPlans()->with('book');
        if (request('status')) {
            $query->where('status', request('status'));
        }
        $readingPlans = $query->orderBy('target_date')->get();

        return view('reading-plans.index', [
            'readingPlans' => $readingPlans,
            'currentStatus' => request('status'),
        ]);
    }

    /**
     * 読書計画の作成画面を表示
     */
    public function create(): View
    {
        $books = Book::orderBy('title')->get();

        return view('reading-plans.create', compact('books'));
    }

    /**
     * 読書計画を登録する処理
     */
    public function store(StoreReadingPlanRequest $request): RedirectResponse
    {
        $request->user()->readingPlans()->create([
            'book_id' => $request->validated('book_id'),
            'target_date' => $request->validated('target_date'),
            'status' => ReadingPlanStatus::InProgress,
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を登録しました。');
    }

    /**
     * 読書計画の編集画面を表示
     */
    public function edit(ReadingPlan $readingPlan): View
    {
        $this->authorize('update', $readingPlan);
        $readingPlan->load('book');

        return view('reading-plans.edit', compact('readingPlan'));
    }

    /**
     * 読書計画を更新する処理
     */
    public function update(UpdateReadingPlanRequest $request, ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('update', $readingPlan);
        $data = $request->validated();
        if ($readingPlan->status === ReadingPlanStatus::Expired) {
            $data['status'] = ReadingPlanStatus::InProgress;
            $data['completed_at'] = null;
        }
        $readingPlan->update($data);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を更新しました。');
    }

    /**
     * 読書計画を削除する処理
     */
    public function destroy(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('delete', $readingPlan);
        DB::transaction(function () use ($readingPlan) {
            auth()->user()->notifications()
                ->where('data->reading_plan_id', $readingPlan->id)->delete();
            $readingPlan->delete();
        });

        return redirect()->route('reading-plans.index')->with('success', '読書計画を削除しました。');
    }

    /**
     * 読書計画を読了する処理
     */
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
