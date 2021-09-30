<?php

namespace App\Http\Controllers;

use App\Models\Localize;
use App\Models\LocItem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function list()
    {
        $projects = Project::all();
        return view('list', compact('projects'));
    }

    public function store(Request $request)
    {
        $projectName = $request->get('project_name');
        $lang = $request->get('lang');
        $data['hash'] = sha1($projectName) . time();


        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $path = $this->storeFile($request->file('file'), $data['hash']);
            $data['items'] = json_decode(file_get_contents($path), true);
            $data['name'] = $lang;
        }

        $project = Project::create([
            'hash' => $data['hash'],
            'project_name' => $projectName,
            'lang' => $lang
        ]);

        // создаем локализацию + файл для него
        $localize = $project->createLocalize($data);
        // создаем
        $project->createLocItems($localize, $data);

        return redirect()->route('index.show', ['hash' => $data['hash']]);
    }

    public function show($hash)
    {

        $project = Project::getByHash($hash);

        return view('show', compact('project'));

    }

    public function download(Request $request, $hash)
    {
        $project = Project::getByHash($hash);

        $array = $request->get('row');
        foreach ($array as $item) {
            $data[$item['origin']] = $item['new_trans'];
        }
        $filename =  time() . '.json';
        Storage::disk('local')->put('/localize/' . $filename, json_encode($data, JSON_UNESCAPED_UNICODE));

        $headers = [
            'Content-Type' => 'application/json',
        ];

        return response()->download(storage_path('/app/localize/') . $filename, $project->lang . '.json', $headers);

    }

    public function storeItem(Request $request)
    {
        $label = explode(":", $request->get('label'));

        $res = LocItem::findOrFail($label[0])->update([
            $label[1] => $request->get('text')
        ]);

        return $res;
    }

    public function storeFile($file, $hash = null)
    {
        $hash = is_null($hash) ? time() : $hash;
        $name = $hash . '_' . $file->getClientOriginalName() . '.'.$file->getClientOriginalExtension();
        $destinationPath = storage_path('/app/localize');
        $file->move($destinationPath, $name);
        $path = $destinationPath . '/' . $name;

        return $path;
    }
}
