<?php

namespace App\Http\Controllers;

use App\Events\DiceUpdated;
use App\Jobs\HandleDiceNotificationJob;
use App\Models\Dice;
use App\Models\User;
use App\Models\DiceConfig;
use App\Models\DiceTable;
use App\Models\DiceRow;

use Illuminate\Http\Request;


class DiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $sessions = $user->dices() // truy vấn qua quan hệ belongsToMany
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $canViewConfig = $user->can('Xem cấu hình');
        $canCreateDice = $user->can('Tạo bảng chơi');

        return view('dice.index', compact('sessions', 'canViewConfig', 'canCreateDice'));
    }

    public function create()
    {
        if (!auth()->user()->can('Tạo bảng chơi')) {
            return redirect()->route('dice.index');
        }
        $session = Dice::orderBy('created_at', 'desc')->first();
        $configs = DiceConfig::all();
        return view('dice.create', compact('session', 'configs'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('Tạo bảng chơi')) {
            return redirect()->route('dice.index');
        }
        $config = DiceConfig::where('id', $request->config_id)->firstOrFail();
        $diceParent = Dice::create([
            'name' => $request->name,
            'chat_id' => $request->chat_id,
            'u1' => $request->u1,
            'u2' => $request->u2,
            'u3' => $request->u3,
            'u4' => $request->u4,
            'td' => $config->td,
            'cc' => $config->cc,
            'ts' => $config->ts,
            'same_row' => $config->same_row,
        ]);

        $dice = DiceTable::create([
            'parent_id' => $diceParent->id,
            'date_check' => date('Y-m-d'),
        ]);

        for ($i = 0; $i < 15; $i++) {

            DiceRow::create([
                'dice_id' => $dice->id,
                'is_lock' => !($i == 0),
                'is_show_bo' => $i == 0 && $diceParent->same_row,
            ]);
        }

        $user = auth()->user();
        $relatedUserIds = collect([$user->id]);

        if ($user->hasRole('staff') && $user->staff_for) {
            $relatedUserIds->push($user->staff_for);
        }

        foreach (['super_id', 'master_id'] as $field) {
            if (!is_null($user->$field)) {
                $relatedUserIds->push($user->$field);
            }
        }

        if ($user->hasRole('super') || $user->hasRole('master') || $user->hasRole('admin')) {
            $staffs = User::where('staff_for', $user->id)->pluck('id');
            $relatedUserIds = $relatedUserIds->merge($staffs);
        }

        if ($user->hasRole('staff') && $user->staff_for) {
            $parent = User::find($user->staff_for);
            if ($parent) {
                $staffs = User::where('staff_for', $parent->id)->pluck('id');
                $relatedUserIds = $relatedUserIds->merge($staffs);
            }
        }

        $relatedUserIds = $relatedUserIds->unique();

        $diceParent->users()->attach($relatedUserIds);
        $dice->users()->attach($relatedUserIds);




        return redirect()->route('dice.show', $diceParent->id)
            ->with('success', 'Dice session created successfully.');
    }

    public function get($id)
    {
        $res = [];

        // $sessions = DiceTable::where('parent_id', $id)
        //     ->orderBy('id')->get();

        $sessions = auth()->user()->diceTables()->where('parent_id', $id)
            ->orderBy('id')->get();

        foreach ($sessions as $dice) {
            $diceRows = DiceRow::where('dice_id', $dice->id)->orderBy('id')->get();
            $rows = [];

            foreach ($diceRows as $diceRow) {
                $rows[] = [
                    'value' => [$diceRow->c1, $diceRow->c2, $diceRow->c3, $diceRow->c4],
                    'type' => $diceRow->is_lock ? 'readonly' : '',
                    'id' => $diceRow->id,
                    'same_cell' => $diceRow->same_cell,
                    'same_rows' => [$diceRow->s1, $diceRow->s2, $diceRow->s3, $diceRow->s4],
                    'is_show_bo' => $diceRow->is_show_bo
                ];
            }

            $res = $this->getRes($dice, $rows, $dice, $res);
        }

        return $res;
    }


    public function getFromId($id, Request $request)
    {
        $res = [];

        $sessions = DiceTable::where('parent_id', $id)
            ->where('id', '>=', $request->diceId)
            ->orderBy('id')
            ->get();

        foreach ($sessions as $dice) {
            $rows = [];

            $res = $this->getRes($dice, $rows, $dice, $res);
        }


        return $res;
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user->dices->contains($id)) {
            return redirect('/dice')->with('error', 'Bạn không có quyền xem session này.');
        }

        $session = Dice::findOrFail($id);

        return view('dice.show', compact('session'));
    }

    public function edit(Dice $dice)
    {
        if (!auth()->user()->can('Cập nhật bảng chơi')) {
            return redirect()->route('dice.index');
        }
        return view('dice.edit', compact('dice'));
    }

    public function updateDice(Request $request, Dice $dice)
    {
        if (!auth()->user()->can('Cập nhật bảng chơi')) {
            return redirect()->route('dice.index');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:dices,name,' . $dice->id,
            'chat_id' => 'max:255',
            'u1' => 'required|string|max:255',
            'u2' => 'required|string|max:255',
            'u3' => 'required|string|max:255',
            'u4' => 'required|string|max:255',
        ]);

        $dice->update($validated);

        return redirect()->route('dice.index')
            ->with('success', 'Cấu hình đã được cập nhật thành công.');
    }

    public function unlockRow(Request $request, $row_id)
    {

        $diceRow = DiceRow::where('id', $row_id)
            ->firstOrFail();
        $dice_table_id = $diceRow->dice_id;

        $dice_id = $diceRow->dice->diceParent->id;
        $title = $diceRow->dice->diceParent->name;
        $chat_id = $diceRow->dice->diceParent->chat_id;

        $oldDice = $diceRow->dice;

        $conditionCc = $diceRow->dice->diceParent->cc;
        $conditionTd = $diceRow->dice->diceParent->td;
        $conditionTienSau = $diceRow->dice->diceParent->ts;

        $c1 = ($request->c1 ?? 0);
        $c2 = ($request->c2 ?? 0);
        $c3 = ($request->c3 ?? 0);
        $c4 = ($request->c4 ?? 0);


        $c1 -= $diceRow->s1;
        $c2 -= $diceRow->s2;
        $c3 -= $diceRow->s3;
        $c4 -= $diceRow->s4;

        $td1 = $c1 + ($oldDice->td1 ?? 0);
        $td2 = $c2 + ($oldDice->td2 ?? 0);
        $td3 = $c3 + ($oldDice->td3 ?? 0);
        $td4 = $c4 + ($oldDice->td4 ?? 0);


        $cc1 = ((intval($diceRow->c1) >= $conditionCc ? -$conditionTienSau : 0) + $oldDice->cc1 ?? 0) - $diceRow->sv1;
        $cc2 = ((intval($diceRow->c2) >= $conditionCc ? -$conditionTienSau : 0) + $oldDice->cc2 ?? 0) - $diceRow->sv2;
        $cc3 = ((intval($diceRow->c3) >= $conditionCc ? -$conditionTienSau : 0) + $oldDice->cc3 ?? 0) - $diceRow->sv3;
        $cc4 = ((intval($diceRow->c4) >= $conditionCc ? -$conditionTienSau : 0) + $oldDice->cc4 ?? 0) - $diceRow->sv4;


        $tc1 = $c1 + $oldDice->tc1;
        $tc2 = $c2 + $oldDice->tc2;
        $tc3 = $c3 + $oldDice->tc3;
        $tc4 = $c4 + $oldDice->tc4;

        $tt1 = ($tc1 * $conditionTd) - $cc1;
        $tt2 = ($tc2 * $conditionTd) - $cc2;
        $tt3 = ($tc3 * $conditionTd) - $cc3;
        $tt4 = ($tc4 * $conditionTd) - $cc4;

        $occ1 = $oldDice->cc1 - $cc1;
        $occ2 = $oldDice->cc2 - $cc2;
        $occ3 = $oldDice->cc3 - $cc3;
        $occ4 = $oldDice->cc4 - $cc4;

        $otc1 = $oldDice->tc1 - $tc1;
        $otc2 = $oldDice->tc2 - $tc2;
        $otc3 = $oldDice->tc3 - $tc3;
        $otc4 = $oldDice->tc4 - $tc4;

        $ott1 = $oldDice->tt1 - $tt1;
        $ott2 = $oldDice->tt2 - $tt2;
        $ott3 = $oldDice->tt3 - $tt3;
        $ott4 = $oldDice->tt4 - $tt4;
        $updateData = [
            'td1' => $td1,
            'td2' => $td2,
            'td3' => $td3,
            'td4' => $td4,

            'cc1' => $cc1,
            'cc2' => $cc2,
            'cc3' => $cc3,
            'cc4' => $cc4,

            'tc1' => $tc1,
            'tc2' => $tc2,
            'tc3' => $tc3,
            'tc4' => $tc4,

            'tt1' => $tt1,
            'tt2' => $tt2,
            'tt3' => $tt3,
            'tt4' => $tt4,
        ];

        $res = $this->getArrResEvent($diceRow->dice->id, $updateData, false);
        event(new DiceUpdated([$c1, $c2, $c3, $c4], $dice_id, $dice_table_id, $res, null, $row_id, 'unlock'));
        $oldDice->update($updateData);

        $diceRow->update([
            'is_lock' => false,
            'c1' => null,
            'c2' => null,
            'c3' => null,
            'c4' => null,
            's1' => null,
            's2' => null,
            's3' => null,
            's4' => null,
            'sv1' => null,
            'sv2' => null,
            'sv3' => null,
            'sv4' => null,
            'same_cell' => null,
            'is_show_bo' => true
        ]);

        $diceUpdateList = DiceTable::where('parent_id', $diceRow->dice->diceParent->id)
            ->where('id', '>', $diceRow->dice->id)
            ->orderBy('id')
            ->get();


        foreach ($diceUpdateList as $item) {

            $cc1 = $item->cc1 - $occ1;
            $cc2 = $item->cc2 - $occ2;
            $cc3 = $item->cc3 - $occ3;
            $cc4 = $item->cc4 - $occ4;

            $tc1 = $item->tc1 - $otc1;
            $tc2 = $item->tc2 - $otc2;
            $tc3 = $item->tc3 - $otc3;
            $tc4 = $item->tc4 - $otc4;

            $tt1 = $item->tt1 - $ott1;
            $tt2 = $item->tt2 - $ott2;
            $tt3 = $item->tt3 - $ott3;
            $tt4 = $item->tt4 - $ott4;

            $item->update([
                'cc1' => $cc1,
                'cc2' => $cc2,
                'cc3' => $cc3,
                'cc4' => $cc4,
                'tc1' => $tc1,
                'tc2' => $tc2,
                'tc3' => $tc3,
                'tc4' => $tc4,
                'tt1' => $tt1,
                'tt2' => $tt2,
                'tt3' => $tt3,
                'tt4' => $tt4,
            ]);

        }


        dispatch(new HandleDiceNotificationJob($dice_id, $title, $chat_id, $this));

        return $res;
    }

    public function update(Request $request, $row_id)
    {

        $diceRow = DiceRow::where('id', $row_id)
            ->firstOrFail();

        $dice_id = $diceRow->dice->diceParent->id;
        $dice_table_id = $diceRow->dice_id;
        $title = $diceRow->dice->diceParent->name;
        $chat_id = $diceRow->dice->diceParent->chat_id;
        $c1 = ($request->c1 ?? null);
        $c2 = ($request->c2 ?? null);
        $c3 = ($request->c3 ?? null);
        $c4 = ($request->c4 ?? null);

        $oldDice = $diceRow->dice;


        $conditionCc = $diceRow->dice->diceParent->cc;
        $conditionTd = $diceRow->dice->diceParent->td;
        $conditionTienSau = $diceRow->dice->diceParent->ts;
        $sr = $request->sr;

        $s1 = $request->sv1 ?? null;
        $s2 = $request->sv2 ?? null;
        $s3 = $request->sv3 ?? null;
        $s4 = $request->sv4 ?? null;

        $sv1 = $sr == 1 ? $conditionTienSau : null;
        $sv2 = $sr == 2 ? $conditionTienSau : null;
        $sv3 = $sr == 3 ? $conditionTienSau : null;
        $sv4 = $sr == 4 ? $conditionTienSau : null;

        if ($sr != null) {
            $same_row_value = $oldDice->diceParent->same_row;
            $s1 = $sr == 1 ? $same_row_value : - ($same_row_value / 3);
            $s2 = $sr == 2 ? $same_row_value : - ($same_row_value / 3);
            $s3 = $sr == 3 ? $same_row_value : - ($same_row_value / 3);
            $s4 = $sr == 4 ? $same_row_value : - ($same_row_value / 3);
        }

        $c1 += $s1;
        $c2 += $s2;
        $c3 += $s3;
        $c4 += $s4;

        $td1 = $c1 + ($oldDice->td1 ?? 0);
        $td2 = $c2 + ($oldDice->td2 ?? 0);
        $td3 = $c3 + ($oldDice->td3 ?? 0);
        $td4 = $c4 + ($oldDice->td4 ?? 0);


        $cc1 = ((intval($c1) >= $conditionCc ? $conditionTienSau : 0) + $oldDice->cc1 ?? 0) + $sv1;
        $cc2 = ((intval($c2) >= $conditionCc ? $conditionTienSau : 0) + $oldDice->cc2 ?? 0) + $sv2;
        $cc3 = ((intval($c3) >= $conditionCc ? $conditionTienSau : 0) + $oldDice->cc3 ?? 0) + $sv3;
        $cc4 = ((intval($c4) >= $conditionCc ? $conditionTienSau : 0) + $oldDice->cc4 ?? 0) + $sv4;


        $tc1 = $c1 + $oldDice->tc1;
        $tc2 = $c2 + $oldDice->tc2;
        $tc3 = $c3 + $oldDice->tc3;
        $tc4 = $c4 + $oldDice->tc4;


        $tt1 = ($tc1 * $conditionTd) - $cc1;
        $tt2 = ($tc2 * $conditionTd) - $cc2;
        $tt3 = ($tc3 * $conditionTd) - $cc3;
        $tt4 = ($tc4 * $conditionTd) - $cc4;


        $occ1 = $cc1 - $oldDice->cc1;
        $occ2 = $cc2 - $oldDice->cc2;
        $occ3 = $cc3 - $oldDice->cc3;
        $occ4 = $cc4 - $oldDice->cc4;

        $otc1 = $tc1 - $oldDice->tc1;
        $otc2 = $tc2 - $oldDice->tc2;
        $otc3 = $tc3 - $oldDice->tc3;
        $otc4 = $tc4 - $oldDice->tc4;

        $ott1 = $tt1 - $oldDice->tt1;
        $ott2 = $tt2 - $oldDice->tt2;
        $ott3 = $tt3 - $oldDice->tt3;
        $ott4 = $tt4 - $oldDice->tt4;

        $dataUpdate = [
            'td1' => $td1,
            'td2' => $td2,
            'td3' => $td3,
            'td4' => $td4,

            'cc1' => $cc1,
            'cc2' => $cc2,
            'cc3' => $cc3,
            'cc4' => $cc4,

            'tc1' => $tc1,
            'tc2' => $tc2,
            'tc3' => $tc3,
            'tc4' => $tc4,

            'tt1' => $tt1,
            'tt2' => $tt2,
            'tt3' => $tt3,
            'tt4' => $tt4,
        ];

        $nextDiceRow = $oldDice->diceRows
            ->where('id', '>', $diceRow->id)
            ->sortBy('id')
            ->first();

        $is_unlock_next_row = $request->row_next_id != null
            && $nextDiceRow->c1 == null && $nextDiceRow->c2 == null && $nextDiceRow->c3 == null && $nextDiceRow->c4 == null;

        $res = $this->getArrResEvent($diceRow->dice->id, $dataUpdate, $is_unlock_next_row);


        event(new DiceUpdated(
            [
                $request->c1 ?? null ?? $diceRow->c1,
                $request->c2 ?? null ?? $diceRow->c2,
                $request->c3 ?? null ?? $diceRow->c3,
                $request->c4 ?? null ?? $diceRow->c4
            ],
            $dice_id,
            $dice_table_id,
            $res,
            null,
            $row_id,
            'update',
            $sr != null,
            [
                $s1 ?? $diceRow->s1,
                $s2 ?? $diceRow->s2,
                $s3 ?? $diceRow->s3,
                $s4 ?? $diceRow->s4,
            ],
            $sr
        ));


        $diceRow->update([
            'is_lock' => $request->is_lock,
            'c1' => $request->c1 ?? null ?? $diceRow->c1,
            'c2' => $request->c2 ?? null ?? $diceRow->c2,
            'c3' => $request->c3 ?? null ?? $diceRow->c3,
            'c4' => $request->c4 ?? null ?? $diceRow->c4,
            's1' => $s1 ?? $diceRow->s1,
            's2' => $s2 ?? $diceRow->s2,
            's3' => $s3 ?? $diceRow->s3,
            's4' => $s4 ?? $diceRow->s4,
            'sv1' => $sv1 ?? $diceRow->sv1,
            'sv2' => $sv2 ?? $diceRow->sv2,
            'sv3' => $sv3 ?? $diceRow->sv3,
            'sv4' => $sv4 ?? $diceRow->sv4,
            'same_cell' => $request->sr ?? $diceRow->same_cell,
            'is_show_bo' => false,
        ]);

        $oldDice->update($dataUpdate);

        $diceUpdateList = DiceTable::where('parent_id', $diceRow->dice->diceParent->id)
            ->where('id', '>', $diceRow->dice->id)
            ->orderBy('id')
            ->get();

        if ($request->row_next_id != null) {
            $diceRowUpdate = DiceRow::where('id', $request->row_next_id)
                ->whereNull('c1')
                ->whereNull('c2')
                ->whereNull('c3')
                ->whereNull('c4')
                ->first();

            if ($diceRowUpdate) {
                $diceRowUpdate->update([
                    'is_lock' => !($request->is_lock == true),
                    'is_show_bo' => $request->is_lock == true
                ]);
            }


            foreach ($diceUpdateList as $item) {
                $cc1 = $item->cc1 + $occ1;
                $cc2 = $item->cc2 + $occ2;
                $cc3 = $item->cc3 + $occ3;
                $cc4 = $item->cc4 + $occ4;

                $tc1 = $item->tc1 + $otc1;
                $tc2 = $item->tc2 + $otc2;
                $tc3 = $item->tc3 + $otc3;
                $tc4 = $item->tc4 + $otc4;

                $tt1 = $item->tt1 + $ott1;
                $tt2 = $item->tt2 + $ott2;
                $tt3 = $item->tt3 + $ott3;
                $tt4 = $item->tt4 + $ott4;

                $item->update([
                    'cc1' => $cc1,
                    'cc2' => $cc2,
                    'cc3' => $cc3,
                    'cc4' => $cc4,
                    'tc1' => $tc1,
                    'tc2' => $tc2,
                    'tc3' => $tc3,
                    'tc4' => $tc4,
                    'tt1' => $tt1,
                    'tt2' => $tt2,
                    'tt3' => $tt3,
                    'tt4' => $tt4,
                ]);
            }
        } else {
            if ($diceUpdateList->count() != 0) {

                foreach ($diceUpdateList as $item) {
                    $cc1 = $item->cc1 + $occ1;
                    $cc2 = $item->cc2 + $occ2;
                    $cc3 = $item->cc3 + $occ3;
                    $cc4 = $item->cc4 + $occ4;

                    $tc1 = $item->tc1 + $otc1;
                    $tc2 = $item->tc2 + $otc2;
                    $tc3 = $item->tc3 + $otc3;
                    $tc4 = $item->tc4 + $otc4;

                    $tt1 = $item->tt1 + $ott1;
                    $tt2 = $item->tt2 + $ott2;
                    $tt3 = $item->tt3 + $ott3;
                    $tt4 = $item->tt4 + $ott4;

                    $item->update([
                        'cc1' => $cc1,
                        'cc2' => $cc2,
                        'cc3' => $cc3,
                        'cc4' => $cc4,
                        'tc1' => $tc1,
                        'tc2' => $tc2,
                        'tc3' => $tc3,
                        'tc4' => $tc4,
                        'tt1' => $tt1,
                        'tt2' => $tt2,
                        'tt3' => $tt3,
                        'tt4' => $tt4,
                    ]);
                }
            }
        }
        $countLock = $oldDice->diceRows()
            ->where(function ($query) {
                $query->whereNull('c1')
                    ->orWhereNull('c2')
                    ->orWhereNull('c3')
                    ->orWhereNull('c4');
            })
            ->count();
        if ($countLock <= 2 && $diceUpdateList->count() == 0) {
            $rows = [];

            $dice = DiceTable::create([
                'parent_id' => $oldDice->diceParent->id,
                'date_check' => $oldDice->date_check,
                'cc1' => $oldDice->cc1,
                'cc2' => $oldDice->cc2,
                'cc3' => $oldDice->cc3,
                'cc4' => $oldDice->cc4,

                'tc1' => $oldDice->tc1,
                'tc2' => $oldDice->tc2,
                'tc3' => $oldDice->tc3,
                'tc4' => $oldDice->tc4,

                'tt1' => $oldDice->tt1,
                'tt2' => $oldDice->tt2,
                'tt3' => $oldDice->tt3,
                'tt4' => $oldDice->tt4,
            ]);

            for ($i = 0; $i < 15; $i++) {

                $newDiceRow = DiceRow::create([
                    'dice_id' => $dice->id,
                    'is_lock' => !($i == 0),
                    'is_show_bo' => $i == 0,
                ]);
                $rows[] = [
                    'value' => [$newDiceRow->c1, $newDiceRow->c2, $newDiceRow->c3, $newDiceRow->c4],
                    'type' => $newDiceRow->is_lock ? 'readonly' : '',
                    'id' => $newDiceRow->id,
                    'same_cell' => $newDiceRow->same_cell,
                    'same_rows' => [$newDiceRow->s1, $newDiceRow->s2, $newDiceRow->s3, $newDiceRow->s4],
                    'is_show_bo' => $newDiceRow->is_show_bo

                ];
            }

            $res = $this->getResEvent($dice, $rows, $oldDice);

            event(new DiceUpdated(null, $dice_id, null, $res, null, null, 'new'));
        }

        dispatch(new HandleDiceNotificationJob($dice_id, $title, $chat_id, $this));

        return $res;
    }

    public function newTable(Request $request)
    {

        $oldDice = DiceTable::where('id', $request->dice_id)
            ->firstOrFail();

        $dice_id = $oldDice->diceParent->id;

        $dice = DiceTable::create([
            'parent_id' => $oldDice->diceParent->id,
            'date_check' => $oldDice->date_check,
            'cc1' => $oldDice->cc1,
            'cc2' => $oldDice->cc2,
            'cc3' => $oldDice->cc3,
            'cc4' => $oldDice->cc4,

            'tc1' => $oldDice->tc1,
            'tc2' => $oldDice->tc2,
            'tc3' => $oldDice->tc3,
            'tc4' => $oldDice->tc4,

            'tt1' => $oldDice->tt1,
            'tt2' => $oldDice->tt2,
            'tt3' => $oldDice->tt3,
            'tt4' => $oldDice->tt4,
        ]);

        for ($i = 0; $i < 15; $i++) {

            $newDiceRow = DiceRow::create([
                'dice_id' => $dice->id,
                'is_lock' => !($i == 0),
                'is_show_bo' => $i == 0,
            ]);
            $rows[] = [
                'value' => [$newDiceRow->c1, $newDiceRow->c2, $newDiceRow->c3, $newDiceRow->c4],
                'type' => $newDiceRow->is_lock ? 'readonly' : '',
                'id' => $newDiceRow->id,
                'same_cell' => $newDiceRow->same_cell,
                'same_rows' => [$newDiceRow->s1, $newDiceRow->s2, $newDiceRow->s3, $newDiceRow->s4],
                'is_show_bo' => $newDiceRow->is_show_bo
            ];
        }

        $res = $this->getResEvent($dice, $rows, $oldDice);

        event(new DiceUpdated(null, $dice_id, null, $res, null, null, 'new'));
    }

    public
    function destroy($id)
    {
        if (!auth()->user()->can('Xóa bảng chơi')) {
            return redirect()->route('dice.index');
        }
        $diceIds = Dice::where('id', $id)->pluck('id');

        $diceSubIds = DiceTable::whereIn('parent_id', $diceIds)->pluck('id');

        DiceRow::whereIn('dice_id', $diceSubIds)->delete();

        DiceTable::whereIn('parent_id', $diceIds)->delete();

        Dice::where('id', $id)->delete();

        return redirect()->route('dice.index')
            ->with('success', 'Dice session deleted successfully.');
    }

    /**
     * @param mixed $dice
     * @param array $rows
     * @return array
     */
    public
    function res(mixed $dice, array $rows, $td, $cc, $tc, $tt): array
    {
        return [
            'id' => $dice->id,
            'name' => $dice->diceParent->name,
            'ftd' => $dice->diceParent->td,
            'u1' => $dice->diceParent->u1,
            'u2' => $dice->diceParent->u2,
            'u3' => $dice->diceParent->u3,
            'u4' => $dice->diceParent->u4,
            'date' => \Carbon\Carbon::parse($dice->date_check)->format('j/n/y'),
            'rows' => $rows,
            'td' => $td,
            'cc' => $cc,
            'tc' => $tc,
            'tt' => $tt,
        ];
    }

    /**
     * @param $dice
     * @param array $rows
     * @param $oldDice
     * @param array $res
     * @return array
     */
    public
    function getRes($dice, array $rows, $oldDice, array $res): array
    {
        $user = auth()->user();

        $td = $user->can('readTD') ? [
            $dice->td1 ?? 0,
            $dice->td2 ?? 0,
            $dice->td3 ?? 0,
            $dice->td4 ?? 0,
        ] : null;

        $cc = $user->can('Xem tiền xâu') ? [
            $oldDice->cc1 ?? 0,
            $oldDice->cc2 ?? 0,
            $oldDice->cc3 ?? 0,
            $oldDice->cc4 ?? 0,
        ] : null;

        $tc = $user->can('Xem tiền xâu') ? [
            $oldDice->tc1 ?? 0,
            $oldDice->tc2 ?? 0,
            $oldDice->tc3 ?? 0,
            $oldDice->tc4 ?? 0,
        ] : null;

        $tt = $user->can('Xem tổng tiền') ? [
            $oldDice->tt1 ?? 0,
            $oldDice->tt2 ?? 0,
            $oldDice->tt3 ?? 0,
            $oldDice->tt4 ?? 0,
        ] : null;

        $res[] = $this->res(
            $dice,
            $rows,
            $td,
            $cc,
            $tc,
            $tt
        );

        return $res;
    }


    public
    function getResEvent($dice, array $rows, $oldDice): array
    {

        return $this->res(
            $dice,
            $rows,
            [
                $dice->td1 ?? 0,
                $dice->td2 ?? 0,
                $dice->td3 ?? 0,
                $dice->td4 ?? 0,
            ],
            [
                $oldDice->cc1 ?? 0,
                $oldDice->cc2 ?? 0,
                $oldDice->cc3 ?? 0,
                $oldDice->cc4 ?? 0,
            ],
            [
                $oldDice->tc1 ?? 0,
                $oldDice->tc2 ?? 0,
                $oldDice->tc3 ?? 0,
                $oldDice->tc4 ?? 0,
            ],
            [
                $oldDice->tt1 ?? 0,
                $oldDice->tt2 ?? 0,
                $oldDice->tt3 ?? 0,
                $oldDice->tt4 ?? 0,
            ]
        );
    }

    public
    function verifyPassword(Request $request)
    {
        $password = $request->input('password');

        // You can change this to use a more secure password storage method
        if ($password === '123456') {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 401);
    }

    /**
     * @param $oldDice
     * @return array
     */


    public
    function getArrResEvent($id, $dataUpdate, $is_unlock_next_row): array
    {
        $res[] = [
            'id' => $id,
            'td' => [
                $dataUpdate['td1'] ?? 0,
                $dataUpdate['td2'] ?? 0,
                $dataUpdate['td3'] ?? 0,
                $dataUpdate['td4'] ?? 0,
            ],
            'cc' => [
                $dataUpdate['cc1'] ?? 0,
                $dataUpdate['cc2'] ?? 0,
                $dataUpdate['cc3'] ?? 0,
                $dataUpdate['cc4'] ?? 0,
            ],
            'tc' => [
                $dataUpdate['tc1'] ?? 0,
                $dataUpdate['tc2'] ?? 0,
                $dataUpdate['tc3'] ?? 0,
                $dataUpdate['tc4'] ?? 0,
            ],
            'tt' => [
                $dataUpdate['tt1'] ?? 0,
                $dataUpdate['tt2'] ?? 0,
                $dataUpdate['tt3'] ?? 0,
                $dataUpdate['tt4'] ?? 0,
            ],
            'is_unlock_next_row' => $is_unlock_next_row,
        ];
        return $res;
    }

    private function isHasSameCell($request)
    {

        return $request->s1 != null || $request->s2 != null || $request->s3 != null || $request->s4 != null;
    }

    private function getIndexSameCell($request)
    {
        if ($this->s1 != null) return 1;
        if ($this->s2 != null) return 2;
        if ($this->s3 != null) return 3;
        if ($this->s4 != null) return 4;
        return null;
    }
}
