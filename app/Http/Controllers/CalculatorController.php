<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CalculatorController extends Controller
{
    public $isapres;
    // make the functions to calculate the net income based on chilean factors of income
    public function calculate(Request $request)
    {
        // Validate form data
        $request->validate([
            'baseIncome' => 'required|numeric',
            'gratification' => 'required',
            'afp' => 'required',
            'health' => 'required',
            'healthPercent' => 'required',
            'foodBonus' => 'required|numeric',
            'travelBonus' => 'required|numeric'
        ]);
        $baseIncome = $request->input('baseIncome');
        $gratification = $request->input('gratification');
        $afp = $request->input('afp');
        $healthPercent = $request->input('healthPercent');
        $insurance = $request->input('insurance');
        $foodBonus = $request->input('foodBonus');
        $travelBonus = $request->input('travelBonus');

        $grossIncome = $baseIncome + $gratification;

        $afpDiscount = $this->getAFPDiscount($grossIncome, $afp);
        $healthDiscount = $this->getHealthDiscount($grossIncome, $healthPercent);
        $insuranceDiscount = $this->getInsuranceDiscount($grossIncome, $insurance);
        $incomeTax = $this->getIncomeTax($grossIncome);
        $bonuses = $foodBonus + $travelBonus;

        $totalDiscount = $afpDiscount + $healthDiscount + $insuranceDiscount + $incomeTax;
        $netIncome = $grossIncome - $totalDiscount + $bonuses;
        // return value
        return response()->json([
            'netIncome' => $netIncome,
            'totalDiscount' => $totalDiscount,
            'afpDiscount' => $afpDiscount,
            'healthDiscount' => $healthDiscount,
            'insuranceDiscount' => $insuranceDiscount,
            'incomeTax' => $incomeTax,
            'bonuses' => $bonuses,
            'afp' => $afp,
        ]);
    }

    public function calculateNetIncome()
    {
        // make get request to https://api-trabajador.nusystem.com/instituciones-salud API
        // and return the isapres
        $response = Http::get('https://api-trabajador.nusystem.com/instituciones-salud')->json();
        $this->isapres = $response['data'];
        return view('calculator',[
            'afps' => \App\Models\AFPs::all(),
            'isapres' => $this->isapres,
            'netIncome' => 'unknown'
        ]);
    }

    private function getAFPDiscount($baseIncome, $afp)
    {
        $afpDiscount = 0;
        if ($afp == 'AFP Modelo') {
            $afpDiscount = $baseIncome * 0.1058;
        } elseif ($afp == 'AFP Capital') {
            $afpDiscount = $baseIncome * 0.1144;
        } elseif ($afp == 'AFP Provida') {
            $afpDiscount = $baseIncome * 0.1145;
        } elseif ($afp == 'AFP Habitat') {
            $afpDiscount = $baseIncome * 0.1127;
        } elseif ($afp == 'AFP Planvital') {
            $afpDiscount = $baseIncome * 0.1116;
        } elseif ($afp == 'AFP Cuprum') {
            $afpDiscount = $baseIncome * 0.1144;
        } elseif ($afp == 'AFP Uno') {
            $afpDiscount = $baseIncome * 0.1049;
        }
        return $afpDiscount;
    }

    private function getHealthDiscount($grossIncome, $healthPercent)
    {
        return $grossIncome * ($healthPercent * 0.01);
    }

    private function getInsuranceDiscount($grossIncome,$insurance)
    {
        $insuranceDiscount = 0;
        if ($insurance == 'Fonasa') {
            $insuranceDiscount = 0.07;
        } elseif ($insurance == 'Other') {
            $insuranceDiscount = 0;
        }
        return $grossIncome * $insuranceDiscount;
    }

    private function getIncomeTax($taxableIncome)
    {
        $incomeTax = 0;
        if ($taxableIncome <= 868630.5) {
            $incomeTax = 0;
        } elseif ($taxableIncome > 868630.5 && $taxableIncome <= 1930290) {
            $incomeTax = $taxableIncome * 0.04 - 34745.22;
        } elseif ($taxableIncome > 1930290 && $taxableIncome <= 3217150) {
            $incomeTax = $taxableIncome * 0.08 - 111956.82;
        } elseif ($taxableIncome > 3217150 && $taxableIncome <= 4504010) {
            $incomeTax = $taxableIncome * 0.135 - 288900.27;
        } elseif ($taxableIncome > 4504010 && $taxableIncome <= 5790870) {
            $incomeTax = $taxableIncome * 0.23 - 716781.02;
        } elseif ($taxableIncome > 5790870 && $taxableIncome <= 7721160) {
            $incomeTax = $taxableIncome * 0.304 - 1145305.40;
        } elseif ($taxableIncome > 7721160 && $taxableIncome <= 19946330) {
            $incomeTax = $taxableIncome * 0.35 - 1500478.76;
        } elseif ($taxableIncome > 19946330){
            $incomeTax = $taxableIncome * 0.40 - 1497795.26;
        }
        return $incomeTax;
    }
}
