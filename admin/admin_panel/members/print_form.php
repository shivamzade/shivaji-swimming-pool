<?php
/**
 * Print Member Registration Form
 * 
 * Supports both blank form (for offline filling) and populated form (for records).
 * 
 * @package ShivajiPool
 * @version 1.0
 */

// Load configuration
require_once '../../../config/config.php';
require_once '../../../db_connect.php';

// Check if ID is provided for filled form
$member_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$member = null;

if ($member_id) {
    $member = Member::get_by_id($member_id);
    if ($member) {
        $batch_obj = new Batch($conn);
        $member_batches = $batch_obj->getMemberBatches($member_id);
        if (!empty($member_batches)) {
            $b = $member_batches[0];
            $member['batch_display'] =  date('h:i A', strtotime($b['start_time'])) . ' - ' . date('h:i A', strtotime($b['end_time']));
        }
    }
}

// Helper to print value or dots/space
function pv($val, $is_blank_mode) {
    if ($is_blank_mode) return '';
    return htmlspecialchars($val ?? '');
}

$is_blank = !$member;
$title = $is_blank ? 'New Membership Registration Form' : 'Member Registration Record';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - <?php echo POOL_NAME; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 11pt; line-height: 1.2; color: #000; background: #fff; max-width: 210mm; margin: 0; padding: 10mm; }
        .no-print { text-align: center; margin-bottom: 20px; padding: 10px; background: #f0f0f0; border: 1px solid #ccc; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .logo { font-size: 20pt; font-weight: bold; margin-bottom: 3px; }
        .sub-header { font-size: 10pt; color: #555; }
        .form-title { text-align: center; font-size: 14pt; font-weight: bold; margin: 15px 0; text-transform: uppercase; text-decoration: underline; }
        
        .box-container { display: flex; flex-wrap: wrap; margin-bottom: 10px; }
        .box { flex: 1; border: 1px solid #000; padding: 5px; min-height: 30px; display: flex; align-items: flex-end; }
        .box-label { font-weight: bold; font-size: 9pt; margin-bottom: 2px; display: block; width: 100%; border-bottom: 1px dotted #ccc; }
        .box-value { font-size: 12pt; width: 100%; white-space: nowrap; overflow: hidden; }
        
        .row { display: flex; gap: 8px; margin-bottom: 10px; }
        .col { flex: 1; }
        
        .section-header { background: #eee; font-weight: bold; padding: 3px 8px; border: 1px solid #000; margin-top: 12px; margin-bottom: 8px; font-size: 10pt; }
        
        .photo-box { width: 100px; height: 120px; border: 2px solid #000; display: flex; justify-content: center; align-items: center; text-align: center; margin: 0 auto; margin-right: 0; font-size: 8pt; color: #999; }
        
        .checkbox-group { display: flex; gap: 15px; }
        .checkbox-item { display: flex; align-items: center; }
        .checkbox-box { width: 15px; height: 15px; border: 1px solid #000; margin-right: 5px; display: inline-block; }
        .checkbox-box.checked { background-color: #000; }
        
        .footer { margin-top: 30px; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .signature-box { width: 180px; text-align: center; border-top: 1px solid #000; padding-top: 3px; font-size: 9pt; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .section-header { background-color: #ddd !important; -webkit-print-color-adjust: exact; }
        }
        
        .field-group { border: 1px solid #999; padding: 10px; margin-bottom: 10px; position: relative; }
        .field-legend { position: absolute; top: -10px; left: 10px; background: #fff; padding: 0 5px; font-size: 9pt; font-weight: bold; }
        
        table.office-use { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9pt; }
        table.office-use td, table.office-use th { border: 1px solid #000; padding: 5px; text-align: left; }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14pt; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px;">Print Form</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14pt; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 4px;">Close</button>
    </div>

    <div class="header">
            <div class="logo"><?php echo POOL_NAME; ?></div>
        <div class="sub-header">
            <?php echo get_setting('pool_address', 'Swimming Pool Complex'); ?><br>
            Phone: <?php echo get_setting('pool_phone', 'N/A'); ?> | Email: <?php echo get_setting('pool_email', 'N/A'); ?>
        </div>
        </div>
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div style="flex: 1;">
            <div class="form-title">MEMBERSHIP REGISTRATION FORM</div>
            <?php if (!$is_blank): ?>
                <div style="margin-bottom: 10px;"><strong>Member Code:</strong> <?php echo pv($member['member_code'], false); ?></div>
                <div><strong>Reg. Date:</strong> <?php echo format_date($member['registration_date']); ?></div>
                <div><strong>Batch:</strong> <?php echo pv($member['batch_display'] ?? 'N/A', false); ?></div>
            <?php else: ?>
                <div style="margin-bottom: 10px;"><strong>Date:</strong> _____________________</div>
                <div style="margin-bottom: 10px;"><strong>Batch:</strong> _____________________</div>
            <?php endif; ?>
        </div>
        <div class="photo-box">
            <?php if (!$is_blank && $member['photo_path']): ?>
         <!-- Image support could be added here if path is resolvable -->
          <img src="<?php echo ASSETS_URL . '/uploads/members/' . clean($member['photo_path']); ?>">

            <?php else: ?>
                Affix Recent<br>Passport Size<br>Photograph
            <?php endif; ?>
        </div>
    </div>
    
    <div class="section-header">1. PERSONAL INFORMATION</div>
    
    <div class="row">
        <div class="col">
            <div class="box-label">First Name</div>
            <div class="box-value"><?php echo pv($member['first_name'] ?? '', $is_blank); ?></div>
            <?php if($is_blank) echo str_repeat('_', 30); ?>
        </div>
        <div class="col">
            <div class="box-label">Last Name</div>
            <div class="box-value"><?php echo pv($member['last_name'] ?? '', $is_blank); ?></div>
            <?php if($is_blank) echo str_repeat('_', 30); ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col">
            <div class="box-label">Date of Birth</div>
            <div class="box-value"><?php echo !$is_blank ? format_date($member['date_of_birth']) : ''; ?></div>
             <?php if($is_blank) echo str_repeat('_', 15); ?>
        </div>
        <div class="col">
            <div class="box-label">Gender</div>
            <div class="checkbox-group">
                <div class="checkbox-item"><span class="checkbox-box <?php echo (!$is_blank && $member['gender'] == 'MALE') ? 'checked' : ''; ?>"></span> Male</div>
                <div class="checkbox-item"><span class="checkbox-box <?php echo (!$is_blank && $member['gender'] == 'FEMALE') ? 'checked' : ''; ?>"></span> Female</div>
                <div class="checkbox-item"><span class="checkbox-box <?php echo (!$is_blank && $member['gender'] == 'OTHER') ? 'checked' : ''; ?>"></span> Other</div>
            </div>
        </div>
        <div class="col">
             <div class="box-label">Blood Group</div>
             <div class="box-value"><?php echo pv($member['blood_group'] ?? '', $is_blank); ?></div>
             <?php if($is_blank) echo str_repeat('_', 10); ?>
        </div>
    </div>
    
    <div class="section-header">2. CONTACT DETAILS</div>
    
    <div class="row">
        <div class="col">
            <div class="box-label">Phone Number (WhatsApp)</div>
            <div class="box-value"><?php echo pv($member['phone'] ?? '', $is_blank); ?></div>
             <?php if($is_blank) echo str_repeat('_', 25); ?>
        </div>
        <div class="col">
            <div class="box-label">Alternate Phone</div>
            <div class="box-value"><?php echo pv($member['alternate_phone'] ?? '', $is_blank); ?></div>
             <?php if($is_blank) echo str_repeat('_', 25); ?>
        </div>
        <div class="col">
            <div class="box-label">Email Address</div>
            <div class="box-value"><?php echo pv($member['email'] ?? '', $is_blank); ?></div>
             <?php if($is_blank) echo str_repeat('_', 50); ?>
        </div>
    </div>
    
    
    <div class="row">
        <div class="col-12">
            <div class="box-label">Residential Address</div>
             <div class="box-value">
                <?php 
                if(!$is_blank) {
                    echo clean($member['address_line1']) . ', ' . clean($member['address_line2']) . '<br>';
                    echo clean($member['city']) . ', ' . clean($member['state']) . ' - ' . clean($member['pincode']);
                }
                ?>
            </div>
            
            <?php if($is_blank): ?>
            <div style="height: 30px; border-bottom: 1px dotted #ccc;"></div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="section-header">3. MEDICAL & EMERGENCY</div>
    
    <div class="row">
        <div class="col-12">
            <div class="box-label">Medical Conditions / Allergies (if any)</div>
            <div class="box-value"><?php echo pv($member['medical_conditions'] ?? '', $is_blank); ?></div>
            <?php if($is_blank) echo '<div style="margin-top:15px; border-bottom:1px dotted #ccc;"></div>'; ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col">
            <div class="box-label">Emergency Contact Name</div>
            <div class="box-value"><?php echo pv($member['emergency_contact_name'] ?? '', $is_blank); ?></div>
            <?php if($is_blank) echo str_repeat('_', 25); ?>
        </div>
        <div class="col">
            <div class="box-label">Relationship</div>
            <div class="box-value"><?php echo pv($member['emergency_contact_relation'] ?? '', $is_blank); ?></div>
            <?php if($is_blank) echo str_repeat('_', 25); ?>
        </div>
        <div class="col">
            <div class="box-label">Emergency Phone</div>
            <div class="box-value"><?php echo pv($member['emergency_contact_phone'] ?? '', $is_blank); ?></div>
            <?php if($is_blank) echo str_repeat('_', 25); ?>
        </div>
    </div>

      <div class="section-header"> Medical Certificate</div>
    
    <div style="font-size: 9pt; text-align: justify; margin-bottom: 20px;">
        This is to certify that 
        <?php echo (!$is_blank && !empty($member['first_name'])) ? clean($member['first_name']) : '__________'; ?> 
        <?php echo (!$is_blank && !empty($member['last_name'])) ? clean($member['last_name']) : '__________'; ?> 
        is physically fit to undertake swimming activities. He/She is also free from skin diseases and is not suffering from any infectious disease/infection.

        <!-- I agree to abide by all the rules and regulations of <strong><?php echo POOL_NAME; ?></strong>. 
        I understand that the management is not responsible for any loss of personal belongings or injuries sustained within the premises due to negligence. -->
    </div>
    
    <div class="footer" style="justify-content: flex-end;">
        
       
         <div class="signature-box" >
            Authorized Medical Officer MBBS/MD Doctor <br>(Signature & Stamp)
        </div>
      
    </div>
    
   
    


   

     <div class="section-header"> Declaration</div>
    
    <div style="font-size: 9pt; text-align: justify; margin-bottom: 20px;">
       
        I hereby declare that all the information provided above is true and correct to the best of my knowledge and belief.
        I have read and understood the rules and regulations of <?php echo POOL_NAME; ?> and agree to abide by them.
    </div>
    
    <div class="footer">
        
        <div class="signature-box">
            For <?php echo POOL_NAME; ?><br>(Authorized Signatory)
        </div>
        
        <div class="signature-box">
            Signature of Applicant
        </div>
    </div>

        <div class="section-header" style="background-color: #f9f9f9; margin-top: 25px;">For Office Use Only</div>
    
    <table class="office-use">
        <tr>
            <td width="30%"><strong>Membership Type:</strong><br><br>□ Daily □ Monthly □ Quarterly □ Yearly</td>
            <td width="30%"><strong>Payment Details:</strong><br><br>Amount: ___________  <br>Receipt No: ___________</td>
            <td width="40%"><strong>Validity:</strong><br><br>From: _______________ To: _______________</td>
        </tr>
    </table>

</body>
</html>
