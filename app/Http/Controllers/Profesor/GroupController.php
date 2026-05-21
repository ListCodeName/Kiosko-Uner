<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Get all groups for the authenticated professor
     */
    public function index()
    {
        $groups = Group::where('professor_id', Auth::id())
            ->withCount('students')
            ->with('students:id,name,username')
            ->get();

        return response()->json($groups);
    }

    /**
     * Store a newly created group
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'professor_id' => Auth::id(),
        ]);

        // Load relations/counts for the response
        $group->loadCount('students');
        $group->load('students:id,name,username');

        return response()->json(['message' => 'Grupo creado con éxito', 'group' => $group], 201);
    }

    /**
     * Update the specified group
     */
    public function update(Request $request, Group $group)
    {
        // Ensure the professor owns the group
        if ($group->professor_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $group->update($validated);

        return response()->json(['message' => 'Grupo actualizado', 'group' => $group]);
    }

    /**
     * Remove the specified group
     */
    public function destroy(Group $group)
    {
        if ($group->professor_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $group->delete();

        return response()->json(['message' => 'Grupo eliminado']);
    }

    /**
     * Add a student to the group
     */
    public function addStudent(Request $request, Group $group)
    {
        if ($group->professor_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Verify the user is an 'alumno'
        $user = User::find($validated['user_id']);
        if ($user->role !== 'alumno') {
            return response()->json(['message' => 'El usuario no es un alumno válido'], 422);
        }

        // Attach without detaching or duplicating
        $group->students()->syncWithoutDetaching([$validated['user_id']]);

        return response()->json([
            'message' => 'Alumno agregado al grupo',
            'student' => $user->only(['id', 'name', 'username'])
        ]);
    }

    /**
     * Remove a student from the group
     */
    public function removeStudent(Group $group, User $user)
    {
        if ($group->professor_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $group->students()->detach($user->id);

        return response()->json(['message' => 'Alumno quitado del grupo']);
    }

    /**
     * Search available students
     */
    public function searchStudents(Request $request)
    {
        $students = User::where('role', 'alumno')
            ->select('id', 'name', 'username')
            ->get();

        if ($request->filled('q')) {
            $search = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($request->input('q')));
            $students = $students->filter(function($student) use ($search) {
                $name = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($student->name));
                $username = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($student->username));
                return str_contains($name, $search) || str_contains($username, $search);
            })->values();
        }

        return response()->json($students->take(20));
    }
}
