<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChartImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|in:power,inverter',
            'image' => 'required|string',
        ]);

        session([$request->name . '_chart_image' => $request->image]);

        return response()->json(['status' => 'ok']);
    }
}
