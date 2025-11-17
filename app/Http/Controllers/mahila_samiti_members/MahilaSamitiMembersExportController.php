<?php

namespace App\Http\Controllers\mahila_samiti_members;

use App\Http\Controllers\Controller;
use App\Models\mahila_samiti_members\AddMahilaSamitiMembers;
use Mpdf\Mpdf;
use Illuminate\Http\Request;

class MahilaSamitiMembersExportController extends Controller
{
    public function exportFPDF(Request $request)
    {
        try {
            $members = AddMahilaSamitiMembers::orderBy('created_at', 'desc')->get();
            
            // Get selected fields from query parameters
            $selectedFields = $request->query('fields', []);
            if (is_string($selectedFields)) {
                $selectedFields = [$selectedFields];
            }
            
            // Default fields if none selected
            if (empty($selectedFields)) {
                $selectedFields = ['session', 'name', 'father_husband', 'type', 'designation', 'anchal', 'mid', 'phone', 'address'];
            }
            
            // Define field mappings
            $fieldLabels = [
                'session' => 'Session',
                'name' => 'Name',
                'father_husband' => 'Father/Husband',
                'type' => 'Type',
                'designation' => 'Designation',
                'anchal' => 'Anchal',
                'mid' => 'MID',
                'phone' => 'Phone',
                'address' => 'Address'
            ];
            
            // Create mPDF instance with UTF-8 and landscape mode
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L', // Landscape
                'margin_left' => 5,
                'margin_right' => 5,
                'margin_top' => 5,
                'margin_bottom' => 5,
            ]);
            
            // Build simple HTML table
            $html = '<h2 style="text-align: center;">Mahila Samiti Members</h2>';
            $html .= '<table cellpadding="5" cellspacing="2" border="1" style="font-size: 10px; width: 100%; border-collapse: collapse;">';
            
            // Header row with selected fields only
            $html .= '<tr style="background-color: #CCCCCC; height: 12px;">';
            foreach ($selectedFields as $field) {
                if (isset($fieldLabels[$field])) {
                    $html .= '<th style="padding: 6px;">' . $fieldLabels[$field] . '</th>';
                }
            }
            $html .= '</tr>';
            
            // Data rows
            foreach ($members as $member) {
                $html .= '<tr style="height: 10px;">';
                
                foreach ($selectedFields as $field) {
                    $value = '';
                    
                    switch ($field) {
                        case 'session':
                            $value = htmlspecialchars(trim($member->session ?? ''), ENT_QUOTES, 'UTF-8');
                            break;
                        case 'name':
                            $value = htmlspecialchars(trim($member->name ?? ''), ENT_QUOTES, 'UTF-8');
                            break;
                        case 'father_husband':
                            $value = htmlspecialchars(trim(($member->husband_name ?? $member->father_name ?? '')), ENT_QUOTES, 'UTF-8');
                            break;
                        case 'type':
                            $value = htmlspecialchars(trim($member->type ?? ''), ENT_QUOTES, 'UTF-8');
                            break;
                        case 'designation':
                            $value = htmlspecialchars(trim($member->designation ?? ''), ENT_QUOTES, 'UTF-8');
                            break;
                        case 'anchal':
                            $value = htmlspecialchars(trim($member->anchal_name ?? ''), ENT_QUOTES, 'UTF-8');
                            break;
                        case 'mid':
                            $value = htmlspecialchars(trim($member->mid ?? ''), ENT_QUOTES, 'UTF-8');
                            break;
                        case 'phone':
                            $value = htmlspecialchars(trim($member->mobile_number ?? ''), ENT_QUOTES, 'UTF-8');
                            break;
                        case 'address':
                            $value = htmlspecialchars(trim(($member->address ?? '') . ', ' . ($member->city ?? '') . ', ' . ($member->state ?? '')), ENT_QUOTES, 'UTF-8');
                            break;
                    }
                    
                    $html .= '<td style="padding: 4px;">' . $value . '</td>';
                }
                
                $html .= '</tr>';
            }
            
            $html .= '</table>';
            
            // Write HTML to PDF
            $mpdf->WriteHTML($html);
            
            // Output PDF
            $mpdf->Output('mahila_samiti_members.pdf', 'D');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
