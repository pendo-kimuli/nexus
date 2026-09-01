<?php

namespace App\Http\Controllers;

use App\Models\ValueDeclaration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValueDeclarationController extends Controller
{
    public function create()
    {
        return view('value-declarations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'skills_offered' => 'required|string',
            'skills_sought' => 'required|string',
        ]);

        $validated['user_id'] = Auth::id();
        ValueDeclaration::create($validated);

        return redirect()->route('value-declarations.index')->with('status', 'Value declaration created.');
    }

    public function index()
    {
        $declarations = ValueDeclaration::where('user_id', Auth::id())->latest()->get();
        return view('value-declarations.index', compact('declarations'));
    }

    public function matches()
    {
        $myDeclarations = ValueDeclaration::where('user_id', Auth::id())->get();
        $others = ValueDeclaration::where('user_id', '!=', Auth::id())->with('user')->get();

        $matches = [];
        foreach ($myDeclarations as $mine) {
            foreach ($others as $other) {
                if ($mine->matchesWith($other)) {
                    $matches[] = ['mine' => $mine, 'match' => $other];
                }
            }
        }

        return view('matches', compact('matches'));
    }
}