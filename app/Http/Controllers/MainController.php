<?php

namespace App\Http\Controllers;

use App\Models\Localize;
use App\Models\LocItem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{

    const LANG = [
        "BG" => "BG - Bulgarian",
        "CS" => "CS - Czech",
        "DA" => "DA - Danish",
        "DE" => "DE - German",
        "EL" => "EL - Greek",
        "EN-GB" => "EN-GB - English (British)",
        "EN-US" => "EN-US - English (American)",
        "ES" => "ES - Spanish",
        "ET" => "ET - Estonian",
        "FI" => "FI - Finnish",
        "FR" => "FR - French",
        "HU" => "HU - Hungarian",
        "ID" => "ID - Indonesian",
        "IT" => "IT - Italian",
        "JA" => "JA - Japanese",
        "LT" => "LT - Lithuanian",
        "LV" => "LV - Latvian",
        "NL" => "NL - Dutch",
        "PL" => "PL - Polish",
        "PT-BR" => "PT-BR - Portuguese (Brazilian)",
        "RO" => "RO - Romanian",
        "RU" => "RU - Russian",
        "SK" => "SK - Slovak",
        "SL" => "SL - Slovenian",
        "SV" => "SV - Swedish",
        "TR" => "TR - Turkish",
        "UK" => "UK - Ukrainian",
        'ZH'  => "Chinese (simplified)",
    ];

    public function index()
    {
        return view('index')->with(['langs' => self::LANG]);
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

    public function deepl($hash)
    {

        $project = Project::getByHash($hash);
        if($project) {
            $token = config('app.deepl');
            if(is_null($token) or empty($token)) {
                return view('show', compact('project'))->withMessage('Deepl APi key is not detected!');
            }
            if(!$project->localize->locItem->isEmpty()) {
                $translator = new \DeepL\Translator($token);
                // $project->lang = "en-US";
                set_time_limit(0);
                foreach ($project->localize->locItem as $item) {
                    if(empty($item['new_trans'])) {

                        if(isset($item['trans']) && !empty($item['trans'])) {
                            $result = $translator->translateText($item['trans'], null, $project->lang);
                        } else {
                            $result = $translator->translateText($item['origin'], null, $project->lang);
                        }

                        if($result) {
                            if(isset($result->text)) {
                                $item->update(['new_trans' => $result->text]);
                            }
                        }
                        usleep(10000);
                    }
                }
            }

        }
        return view('show', compact('project'));

    }

    public function addItem($hash)
    {
        $project = Project::getByHash($hash);
        $item = $project->createLocItems($project->localize, null);

        return $item->id;
    }

    public function destroyItem(Request $request, $hash)
    {
        return LocItem::findOrFail($request->get('id'))->delete();
    }

    public function download(Request $request, $hash)
    {
        $project = Project::getByHash($hash);

        $array = $request->get('row');
        foreach ($array as $item) {
            $data[$item['origin']] = $item['new_trans'];
        }
        $filename =  time() . '.json';
        Storage::disk('local')->put('/localize/' . $filename, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

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
