<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $mustHaves = [
            'Camilla fija',
            'Camilla reclinable',
            'Apoyabrazos',
            'Aro de luz',
            'Acceso 24hs',
            'Acceso con horario limitado',
            'Film',
            'Impresora térmica',
            'Papel térmico',
            'Papel de cocina',
            'Cinta esparadrapo',
            'Rasuradoras',
            'Guantes talle S',
            'Guantes talle M',
            'Guantes talle L',
            'Stencil stuff',
            'Alcohol 70%',
            'Desinfectante',
        ];

        $additionals = [
            'Black ink',
            'Witch hazel',
            'Ink cups chicos',
            'Ink cups medianos',
            'Ink cups grandes',
            'Grip tape',
            'Butter',
            'Bajalenguas',
            'Campos quirúrgicos',
            'Marcadores sharpie',
            'Second skin',
            'Impresora de papel',
            'Autoclave',
        ];

        foreach ($mustHaves as $name) {
            Feature::create(['name' => $name, 'category' => 'must_have']);
        }

        foreach ($additionals as $name) {
            Feature::create(['name' => $name, 'category' => 'additional']);
        }
    }
}