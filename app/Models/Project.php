<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeGetByHash($query, $hash)
    {
        return $query->where('hash', $hash)->firstOrFail();
    }

    public function localize()
    {
        return $this->hasOne(Localize::class);
    }

    public function createLocalize($data = null)
    {
        $locName = isset($data['name']) ? $data['name'] : 'ru';

        if(!isset($data['name'])) {
            Storage::disk('local')->put('localize/'. $data['hash'] . ".json", []);
        }

        return $this->localize()->create([
            'project_id' => $this->id,
            'loc_name' => $locName,
        ]);
    }

    public function createLocItems(Localize $localize, $data = null)
    {
        if(isset($data['items'])) {
            foreach ($data['items'] as $key => $value) {
                $localize->locItem()->create([
                    'localize_id' => $localize->id,
                    'origin' => $key,
                    'trans' => $value,
                    'new_trans' => ''
                ]);
            }

            return true;
        } else {
            return $localize->locItem()->create([
                'localize_id' => $localize->id,
                'origin' => '',
                'trans' => '',
                'new_trans' => ''
            ]);
        }

    }
}
