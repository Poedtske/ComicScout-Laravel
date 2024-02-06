<?php
use App\Models\Serie;
use Illuminate\Http\Request;
use App\Exceptions\SerieAlreadyPresentException;

class SerieService
{
    public function createSerie(Request $request){
        $brotherSeries=self::validate($request);
        $scanlator=$request->input('scanlator_id');
        $serie=Serie::create($request)->scanlator()->associate($scanlator);
    }

    private function checkForDouble($title,$scanlator){
        $series=Serie::all();
        $brotherSeries=[];
        foreach($series as $serie){
            if ($serie->title==$title&&$serie->scanlator_id=$scanlator){
                // throw new Error();
            }
            else{
                if($serie->title==$title)
                $brotherSeries[]=$serie->id;
            }
        }
        if(empty($brotherSeries)){
            return false;
        }
        else{
            return $brotherSeries;
        }
    }

    private function validate(Request $request){
        $title=$request->input('title');
        $scanlator=$request->input('scanlator_id');
        $brotherSeries=self::checkForDouble($title,$scanlator);
        if($brotherSeries==false){
            throw new SerieAlreadyPresentException("serie".$title."is already present in scanlator".$scanlator);
        }
        else{
            return$brotherSeries;
        }
    }


}
