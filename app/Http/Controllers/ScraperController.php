<?php

namespace App\Http\Controllers;

use App\Services\ScraperService;
use App\Models\Scanlator;
use Illuminate\Http\Request;

class ScraperController extends Controller
{

    public function builder(){
        $scanlators=Scanlator::all();
        $ss=new ScraperService($scanlators);
        $ss->scrapeSerie();

        return redirect()
                ->route('home2')
                ->with('success');
    }

    public function serieUpdater(){
        $scanlators=Scanlator::all();
        $ss=new ScraperService($scanlators);
        $ss->updateSeries();

        return redirect()
                ->route('home2')
                ->with('success');
    }
}
