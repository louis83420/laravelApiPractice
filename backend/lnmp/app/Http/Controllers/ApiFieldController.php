<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ApiFieldController extends Controller
{
   public function getFields(Request $request)
   {

        $tableName = $request->query('table');
        if(!Schema::hasTable($tableName)){
            return response()->json(['error'=>'資料表不存在'],404);

        }
        $columns = Schema::getColumnListing($tableName);
        // $requiredFields = [];
        // foreach ($columns as $column) {
        //     $details = Schema::getConnection()->getDoctrineColumn($tableName, $column);
        //     if ($details->getNotnull()) {
        //         $requiredFields[] = $column;
        //     }
        // }
        return response()->json([
            'field'=>$columns
            // 'requiredFields'=>$requiredFields,
        ]);
   }


}
