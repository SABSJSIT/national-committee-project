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
            // Get session filter
            $session = $request->query('session');
            
            // Fetch members with filters
            $query = AddMahilaSamitiMembers::query();
            
            if ($session) {
                $query->where('session', $session);
            }
            
            // Order by type, anchal, and then by designation priority
            $members = $query->orderBy('type')
                            ->orderBy('anchal_name')
                            ->orderBy('designation')
                            ->get();
            
            if ($members->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No members found for export'
                ], 404);
            }
            
            // Group members by Type and then by Anchal
            $groupedMembers = $this->groupMembersByTypeAndAnchal($members);
            
            // Create mPDF instance with UTF-8 and landscape mode
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L', // Landscape
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
            ]);
            
            // Build HTML with grouped format
            $html = $this->buildGroupedHTML($groupedMembers, $session);
            
            // Write HTML to PDF
            $mpdf->WriteHTML($html);
            
            // Output PDF
            $filename = 'mahila_samiti_members_' . ($session ?? 'all') . '.pdf';
            $mpdf->Output($filename, 'D');
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Group members by Type and then by Anchal (or Designation for Sanyojak/Sanyojika)
     */
    private function groupMembersByTypeAndAnchal($members)
    {
        $grouped = [];
        
        // Define type order for proper sorting
        $typeOrder = ['pst' => 1, 'vp-sec' => 2, 'sanyojika' => 3, 'sanyojak' => 3, 'ksm_members' => 4];
        
        foreach ($members as $member) {
            $type = strtolower(trim($member->type));
            $anchal = trim($member->anchal_name);
            
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            
            // For Sanyojak/Sanyojika, group by designation instead of anchal
            $isSanyojak = (stripos($type, 'sanyojak') !== false || stripos($type, 'sanyojika') !== false);
            
            if ($isSanyojak) {
                // Group by designation for Sanyojak/Sanyojika
                $designation = trim($member->designation);
                if (!isset($grouped[$type][$designation])) {
                    $grouped[$type][$designation] = [];
                }
                $grouped[$type][$designation][] = $member;
            } else {
                // Group by anchal for other types
                if (!isset($grouped[$type][$anchal])) {
                    $grouped[$type][$anchal] = [];
                }
                $grouped[$type][$anchal][] = $member;
            }
        }
        
        // Sort by type order
        uksort($grouped, function($a, $b) use ($typeOrder) {
            $orderA = $typeOrder[strtolower($a)] ?? 999;
            $orderB = $typeOrder[strtolower($b)] ?? 999;
            return $orderA - $orderB;
        });
        
        return $grouped;
    }
    
    /**
     * Build HTML with grouped format
     */
    private function buildGroupedHTML($groupedMembers, $session)
    {
        $sessionTitle = $session ? "Session: $session" : "All Sessions";
        
        $html = '
        <style>
            body { font-family: "Noto Sans", Arial, sans-serif; font-size: 9px; }
            h2 { text-align: center; margin-bottom: 5px; font-size: 14px; }
            h3 { text-align: center; margin-bottom: 10px; font-size: 11px; color: #666; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
            th { background-color: #f0f0f0; padding: 6px 4px; border: 1px solid #333; font-weight: bold; text-align: center; font-size: 9px; }
            td { padding: 5px 4px; border: 1px solid #666; font-size: 8px; }
            .type-header { background-color: #e8e8e8; font-weight: bold; padding: 6px; border: 1px solid #333; font-size: 10px; }
            .designation-header { background-color: #f5f5f5; font-weight: bold; padding: 5px; border: 1px solid #666; font-size: 9px; }
            .anchal-header { background-color: #f5f5f5; font-style: italic; padding: 5px; border: 1px solid #666; font-size: 9px; }
            .photo-cell { text-align: center; width: 50px; }
            .sno-cell { text-align: center; width: 40px; }
            .session-cell { width: 60px; }
            .mid-cell { width: 60px; }
            .phone-cell { width: 85px; }
        </style>
        
        <h2>Mahila Samiti Members</h2>
        <h3>' . $sessionTitle . '</h3>
        
        <table>
            <thead>
                <tr>
                    <th class="sno-cell">S.No</th>
                    <th class="session-cell">Session</th>
                    <th class="photo-cell">Photo</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Designation</th>
                    <th class="mid-cell">MID</th>
                    <th>Anchal</th>
                    <th class="phone-cell">Phone</th>
                    <th>City</th>
                    <th>State</th>
                </tr>
            </thead>
            <tbody>';
        
        $serialNo = 1;
        
        foreach ($groupedMembers as $type => $groups) {
            // Type header row
            $typeDisplay = $this->getTypeDisplayName($type);
            $html .= '<tr><td colspan="11" class="type-header">' . strtoupper($typeDisplay) . '</td></tr>';
            
            // Check if this is Sanyojak/Sanyojika type
            $isSanyojak = (stripos($type, 'sanyojak') !== false || stripos($type, 'sanyojika') !== false);
            
            if ($isSanyojak) {
                // For Sanyojak/Sanyojika: groups are by designation
                foreach ($groups as $designation => $members) {
                    // Collect all unique anchals for this designation
                    $anchals = array_unique(array_map(function($m) {
                        return $m->anchal_name;
                    }, $members));
                    
                    // Show designation header first (bold)
                    $html .= '<tr><td colspan="11" class="designation-header">' . htmlspecialchars($designation) . '</td></tr>';
                    
                    // Show anchal list on next line (italic)
                    $anchalList = implode('-', $anchals);
                    $html .= '<tr><td colspan="11" class="anchal-header">Anchal: ' . htmlspecialchars($anchalList) . '</td></tr>';
                    
                    // Member rows
                    foreach ($members as $member) {
                        $html .= '<tr>';
                        $html .= '<td class="sno-cell">' . $serialNo++ . '</td>';
                        $html .= '<td class="session-cell">' . htmlspecialchars($member->session ?? '') . '</td>';
                        $html .= '<td class="photo-cell"></td>'; // Photo placeholder
                        $html .= '<td>' . htmlspecialchars($member->name ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($this->getTypeDisplayName($member->type)) . '</td>';
                        $html .= '<td>' . htmlspecialchars($member->designation ?? '') . '</td>';
                        $html .= '<td class="mid-cell">' . htmlspecialchars($member->mid ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($member->anchal_name ?? '') . '</td>';
                        $html .= '<td class="phone-cell">' . htmlspecialchars($member->mobile_number ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($member->city ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($member->state ?? '') . '</td>';
                        $html .= '</tr>';
                    }
                }
            } else {
                // For other types: groups are by anchal
                foreach ($groups as $anchal => $members) {
                    // Anchal header row (only for types that are anchal-wise, skip for PST)
                    if (strtolower($type) !== 'pst') {
                        $html .= '<tr><td colspan="11" class="anchal-header">Anchal: ' . htmlspecialchars($anchal) . '</td></tr>';
                    }
                    
                    // Member rows
                    foreach ($members as $member) {
                        $html .= '<tr>';
                        $html .= '<td class="sno-cell">' . $serialNo++ . '</td>';
                        $html .= '<td class="session-cell">' . htmlspecialchars($member->session ?? '') . '</td>';
                        $html .= '<td class="photo-cell"></td>'; // Photo placeholder
                        $html .= '<td>' . htmlspecialchars($member->name ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($this->getTypeDisplayName($member->type)) . '</td>';
                        $html .= '<td>' . htmlspecialchars($member->designation ?? '') . '</td>';
                        $html .= '<td class="mid-cell">' . htmlspecialchars($member->mid ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($member->anchal_name ?? '') . '</td>';
                        $html .= '<td class="phone-cell">' . htmlspecialchars($member->mobile_number ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($member->city ?? '') . '</td>';
                        $html .= '<td>' . htmlspecialchars($member->state ?? '') . '</td>';
                        $html .= '</tr>';
                    }
                }
            }
        }

        
        $html .= '</tbody></table>';
        
        return $html;
    }
    
    /**
     * Get display name for type
     */
    private function getTypeDisplayName($type)
    {
        $typeMap = [
            'pst' => 'PST',
            'vp-sec' => 'VP-SEC (Anchal wise)',
            'sanyojika' => 'Sanyojika',
            'ksm_members' => 'KSM Members (Anchal wise)'
        ];
        
        return $typeMap[strtolower($type)] ?? ucfirst($type);
    }
}
