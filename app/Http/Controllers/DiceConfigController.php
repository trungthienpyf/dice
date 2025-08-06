<?php

namespace App\Http\Controllers;

use App\Models\DiceConfig;
use Illuminate\Http\Request;

class DiceConfigController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $canDeleteConfig = $user->can('Xóa cấu hình');
        $canCreateConfig = $user->can('Tạo cấu hình');

        $configs = DiceConfig::latest()
            ->paginate(10);

        return view('dice.configs.index', compact('configs','canDeleteConfig','canCreateConfig'));
    }

    public function create()
    {
        return view('dice.configs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:dice_configs,name',
            'same_row' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if ($value == 0 || $value % 3 !== 0) {
                        $fail('Trường ' . $attribute . ' phải là số khác 0 và chia hết cho 3.');
                    }
                },
            ],
        ]);

        DiceConfig::create([
            'name' => $request->input('name'),
            'td' => $request->input('td'),
            'cc' => $request->input('cc'),
            'ts' => $request->input('ts'),
            'same_row' => $request->input('same_row'),
        ]);

        return redirect()->route('dice.configs.index')
            ->with('success', 'Cấu hình đã được tạo thành công.');
    }

    public function edit(DiceConfig $config)
    {
        return view('dice.configs.edit', compact('config'));
    }

    public function update(Request $request, DiceConfig $config)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'u1' => 'required|string|max:255',
            'u2' => 'required|string|max:255',
            'u3' => 'required|string|max:255',
            'u4' => 'required|string|max:255',
            'min:1',
            'same_row' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value == 0 || $value % 3 !== 0) {
                        $fail('Trường ' . $attribute . ' phải là số khác 0 và chia hết cho 3.');
                    }
                },
            ],
        ]);

        $config->update($validated);

        return redirect()->route('dice.configs.index')
            ->with('success', 'Cấu hình đã được cập nhật thành công.');
    }

    public function destroy(DiceConfig $config)
    {
        $config->delete();

        return redirect()->route('dice.configs.index')
            ->with('success', 'Cấu hình đã được xóa thành công.');
    }
}
