<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LevelController extends Controller
{

    public function index()
    {
        $requests = User::where('level', User::LEVEL_REQUEST)->orderBy('name')->get();
        $admins = User::where('level', User::LEVEL_ADMIN)->orderBy('name')->get();
        $owners = User::where('level', User::LEVEL_OWNER)->orderBy('name')->get();

        return view('level.index', compact('requests', 'admins', 'owners'));
    }

    /**
     * Users are created via the registration page only.
     * This stub prevents a 500 error when /level/create is accessed directly.
     */
    public function create()
    {
        return redirect()->route('level.index')
            ->with('error', 'Pengguna baru dibuat melalui halaman registrasi.');
    }

    /**
     * Stub for POST /level — not used; users register themselves.
     */
    public function store(Request $request)
    {
        return redirect()->route('level.index')
            ->with('error', 'Pengguna baru dibuat melalui halaman registrasi.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        abort_unless(Auth::user()->isOwner(), 403);
        abort_if($user->isOwner(), 403, 'Owner level users cannot be edited from this page.');

        return view('level.edit', compact('user'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        abort_unless(Auth::user()->isOwner(), 403);
        abort_if($user->isOwner(), 403, 'Owner level users cannot be changed from this page.');

        $validated = $request->validate([
            'level' => ['required', 'integer', 'between:0,1'],
        ]);

        $user->update(['level' => (int) $validated['level']]);

        return redirect()->route('level.index')->with('success', 'User level updated successfully.');
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);

        abort_unless(Auth::user()->level >= User::LEVEL_ADMIN, 403);
        abort_unless($user->isRequest(), 403, 'Only request users can be approved.');

        $user->update(['level' => User::LEVEL_ADMIN]);

        return redirect()->route('level.index')->with('success', 'Request approved and promoted to admin.');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        abort_unless(Auth::user()->isOwner(), 403);
        abort_if($user->isOwner(), 403, 'Owner level users cannot be deleted from this page.');

        $user->delete();

        return redirect()->route('level.index')->with('success', 'User deleted successfully.');
    }
}
