<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;

class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('patient')->get();
        return view('admin.rooms', compact('rooms'));
    }
}
