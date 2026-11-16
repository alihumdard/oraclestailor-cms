<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function privacy(): View
    {
        return view('legal.page', [
            'businessSetting' => BusinessSetting::current(),
            'title' => 'Privacy Policy',
            'content' => '
                <h1>Privacy Policy</h1>
                <p>At Saim Tailors, we respect your privacy and are committed to protecting the personal information you share with us.</p>
                <h2>What we collect</h2>
                <ul>
                    <li><strong>Personal details:</strong> Name, phone number, and email address provided during booking or registration.</li>
                    <li><strong>Measurement data:</strong> Body measurements and fit preferences to ensure the perfect garment.</li>
                    <li><strong>Order history:</strong> Details of garments stitched and status updates.</li>
                </ul>
                <h2>How we use it</h2>
                <p>Your information is used solely to provide our tailoring services, arrange fittings, track your order progress, and communicate updates regarding your garments. We do not sell or share your data with third parties.</p>
                <h2>Your Rights</h2>
                <p>You can request to view, correct, or delete your data at any time by contacting us directly.</p>
            ',
        ]);
    }

    public function terms(): View
    {
        return view('legal.page', [
            'businessSetting' => BusinessSetting::current(),
            'title' => 'Terms & Conditions',
            'content' => '
                <h1>Terms & Conditions</h1>
                <p>By booking a fitting with Saim Tailors, you agree to the following terms.</p>
                <h2>Booking & Consultations</h2>
                <p>A fitting request initiates a consultation, not a final contract. Final price, fabric availability, and delivery timelines are confirmed personally by Saim before any work begins.</p>
                <h2>Payments</h2>
                <p>A partial advance is required to initiate the stitching process. The remaining balance is payable upon delivery or collection of the finished garment.</p>
                <h2>Cancellations & Changes</h2>
                <p>Changes to orders must be communicated before the cutting process begins. Cancellation requests after materials have been sourced or tailoring has started may be subject to a fee.</p>
                <h2>Liability</h2>
                <p>We strive for perfection in every stitch. If you are not satisfied with the fit, please inform Saim immediately, and we will do our best to arrange adjustments.</p>
            ',
        ]);
    }

    public function returns(): View
    {
        return view('legal.page', [
            'businessSetting' => BusinessSetting::current(),
            'title' => 'Return & Exchange Policy',
            'content' => '
                <h1>Return & Exchange Policy</h1>
                <p>Every garment is made to measure for you personally, so our return and exchange policy works a little differently to off-the-shelf shopping.</p>
                <h2>Made-to-measure garments</h2>
                <p>Because each piece is cut and stitched to your own measurements, we cannot offer a refund simply for a change of mind once stitching has begun. If something does not fit as expected, tell Saim within 3 days of collection and he will personally arrange the adjustment at no extra cost.</p>
                <h2>Fit adjustments</h2>
                <p>Minor fit issues (length, waist, sleeve) are adjusted free of charge. Please bring the garment back to the workroom so Saim can check it against your original measurements.</p>
                <h2>Fabric or stitching faults</h2>
                <p>If a fabric or stitching fault is found that was not caused by wear, washing, or alteration, Saim will repair or re-stitch the garment free of charge. In rare cases where this is not possible, a partial refund may be agreed.</p>
                <h2>Cancellations before stitching</h2>
                <p>If you cancel before cutting or stitching has started, your advance is refunded in full. Once cutting has begun, the advance covers the fabric and work already done and is non-refundable.</p>
                <h2>How to request a return or exchange</h2>
                <p>Contact Saim directly through the contact form or by phone with your order reference, and he will confirm the next steps with you personally.</p>
            ',
        ]);
    }
}
