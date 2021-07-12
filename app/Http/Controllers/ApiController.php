<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class ApiController extends Controller
{
    public function getAstroid()
    {
        return view('astroidchart');
    }

    public function getAstroidDetail(Request $request)
    {
        $output = array();
        $response = Http::get('https://api.nasa.gov/neo/rest/v1/feed?', [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'detailed' => true,
            'api_key'   => 'xya8XzbE6mF4xPdm523BWhzw8n5DSabf13Igdt3F'
        ]);
        $responseCode = $response->getStatusCode();
        if($responseCode==200)
        {
            $output['status'] = true;
            $res = $response->json();
            $total_data = array();
            $all_astroids = array();
            $astroid_diameter_in_km = array();
            $astroid_velocity_kmph = array();
            $astroid_distance_in_km = array();
            $astroid_min_diameter = array();
            $astroid_max_diameter = array();
            foreach ($res['near_earth_objects'] as $key => $value) {
                $total_data[$key] = $value;
                foreach ($total_data[$key] as $astroid_details_by_date) {
                    $all_astroids[] = $astroid_details_by_date;
                }
            }

            foreach($all_astroids as $astroid)
            {
                foreach ($astroid['estimated_diameter'] as $key => $value) {
                    if ($key == 'kilometers') {
                        $astroid_diameter_in_km[] = $value;
                        array_push($astroid_max_diameter,$value['estimated_diameter_max']);
                        array_push($astroid_min_diameter,$value['estimated_diameter_min']);
                    }
                }

                foreach ($astroid['close_approach_data'] as $close_approach) {
                    foreach ($close_approach['relative_velocity'] as $velocitykey => $value) {
                        if ($velocitykey == 'kilometers_per_hour') {
                            $astroid_velocity_kmph[] = $value;
                        }
                    }
                    foreach ($close_approach['miss_distance'] as $distancekey => $value) {
                        if ($distancekey == 'kilometers') {
                            $astroid_distance_in_km[] = $value;
                        }
                    }
                }
            }
            // echo json_encode($astroid_diameter_in_km);

            $total_days = array_keys($total_data);
            usort($total_days, array($this, "date_sort"));
            
            foreach ($total_days as $key => $value) {
                $total_astroid[$value] = count($total_data[$value]);
            }

            arsort($astroid_velocity_kmph);
            $fastestAseroid = Arr::first($astroid_velocity_kmph);
            $fastestAseroidkey = array_key_first($astroid_velocity_kmph);
            $fastestAseroidId = $all_astroids[$fastestAseroidkey]['id'];

            asort($astroid_distance_in_km);
            $closestAseroid = Arr::first($astroid_distance_in_km);
            $closestAseroidkey = array_key_first($astroid_velocity_kmph);
            $closestAseroidId = $all_astroids[$closestAseroidkey]['id'];

            // echo json_encode($astroid_min_diameter);
            // die;

            $average_diameter = (array_sum($astroid_min_diameter) + array_sum($astroid_max_diameter))/count($all_astroids);

            $dates = array_keys($total_astroid);
            $astroid_by_dates = array_values($total_astroid);
            $output['data'] = array('dates'=>$dates,'astroid_by_dates'=>$astroid_by_dates,'fastestAseroidId'=>$fastestAseroidId, 'fastestAseroid'=>$fastestAseroid, 'closestAseroidId'=>$closestAseroidId, 'closestAseroid'=>$closestAseroid,'average_size'=>$average_diameter);
        }else if($responseCode==400){
            $output['status'] = false;
            $output['data'] = $response->json()['error_message'];
        }else{
            $output['status'] = false;
            $output['data'] = 'Something went wrong!.';
        }
        return response()->json($output);
    }

    public static function date_sort($a, $b) {
        return strtotime($a) - strtotime($b);
    }
}
