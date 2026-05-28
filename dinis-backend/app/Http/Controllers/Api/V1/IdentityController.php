<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IdentityController
{
    public function lookup($nrc)
    {
        // Search the database for the exact NRC
        $citizen = DB::table('citizen_nrc_record')
            ->leftJoin('village', 'citizen_nrc_record.village_id', '=', 'village.village_id')
            ->leftJoin('chiefdom', 'citizen_nrc_record.chiefdom_id', '=', 'chiefdom.chiefdom_id')
            ->where('nrc_number', $nrc)
            ->select('citizen_nrc_record.*', 'village.village_name', 'chiefdom.chiefdom_name')
            ->first();

        if (!$citizen) {
            return response()->json([
                'success' => false,
                'message' => 'NRC Registry: Record not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $citizen
        ], 200);
    }
}