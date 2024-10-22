<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IdeaController extends Controller
{
    public function index():View{

        
        //$ideas = DB::table('ideas')->get();

        $ideas = Idea::all();

        return view("ideas.index", ['ideas' => $ideas]);
    }

    public function create(): View{

        return view("ideas.form");

    }

    public function edit(Idea $idea): View{

        $this->authorize("update", $idea);

        //$idea = Idea::find($idea);
        //dd($idea);
        //return view("ideas.form")->with("idea", $idea);
        return view("ideas.form", ["idea" => $idea]);

    }

    public function store(Request $request): RedirectResponse{

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:300',
        ]);

        Idea::create([
            'user_id' => auth()->user()->id, // $request->user()->id
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        session()->flash("message", "Idea creada correctamente.");

        return redirect()->route("idea.index");

    }

    public function update(Request $request, Idea $idea): RedirectResponse{


        $this->authorize("update", $idea);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:300',
        ]);

        $idea->update($validated);


        session()->flash("message", "Idea actualizada correctamente.");

        return redirect(route("idea.index"));

    }

    public function show(Idea $idea): View{

        return view("ideas.show")->with("idea", $idea);

    }

    public function delete(Idea $idea): RedirectResponse{

        $this->authorize("delete", $idea);

        $idea->delete($idea);


        session()->flash("message", "Idea eliminada.");

        return redirect(route("idea.index"));

    }

    public function synchronizeLikes(Request $request, Idea $idea): RedirectResponse {

        $request->user()->ideasLiked()->toggle([$idea->id]);

        $idea->update(['likes' => $idea->users()->count()]);

        return redirect()->route("idea.show", $idea);
    }

}
