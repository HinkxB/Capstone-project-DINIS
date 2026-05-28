<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FamilyTreeController
{
    /**
     * Retrieve a citizen and their immediate family (parents and children)
     */
    public function show($nrc)
    {
        // 1. FIND THE TARGET CITIZEN
        $targetPerson = DB::table('citizen_nrc_record')
            ->where('nrc_number', $nrc)
            ->first();

        if (!$targetPerson) {
            return response()->json([
                'success' => false,
                'message' => 'Citizen not found in the registry.'
            ], 404);
        }

        // 2. FETCH PARENTS (Looking upwards in the family_lineage table)
        // We join the lineage table to the citizen table to get the parent's actual details
        $parents = DB::table('family_lineage')
            ->join('citizen_nrc_record', 'family_lineage.parent_person_id', '=', 'citizen_nrc_record.person_id')
            ->where('family_lineage.child_person_id', $targetPerson->person_id)
            ->select('citizen_nrc_record.nrc_number', 'citizen_nrc_record.maiden_full_name', 'citizen_nrc_record.sex', 'family_lineage.relationship_type')
            ->get();

        // 3. FETCH CHILDREN (Looking downwards in the family_lineage table)
        // We join the lineage table to the citizen table to get the child's actual details
        $children = DB::table('family_lineage')
            ->join('citizen_nrc_record', 'family_lineage.child_person_id', '=', 'citizen_nrc_record.person_id')
            ->where('family_lineage.parent_person_id', $targetPerson->person_id)
            ->select('citizen_nrc_record.nrc_number', 'citizen_nrc_record.maiden_full_name', 'citizen_nrc_record.sex')
            ->get();

        // 4. RETURN THE STRUCTURED TREE DATA
        return response()->json([
            'success' => true,
            'target' => $targetPerson,
            'parents' => $parents,
            'children' => $children
        ], 200);
    }
}