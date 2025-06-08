<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use GuzzleHttp\Client as GuzzleClient;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'sales_id' => 'required',
            'date' => 'required',
            'total_amount' => 'required|numeric',
            'discount' => 'required|numeric',
            'items' => 'required|array',
            'email' => 'required|email',
        ]);

        $htmlContent = view('emails.receipt', ['data' => $validated])->render();

        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', env('BREVO_API_KEY'));

        $apiInstance = new TransactionalEmailsApi(new GuzzleClient(), $config);

        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => 'Receipt #' . $validated['sales_id'],
            'htmlContent' => $htmlContent,
            'sender' => [
                'name' => 'Alegria Bakeshop',
                'email' => 'josephcajida8@gmail.com',
            ],
            'to' => [
                ['email' => $validated['email'], 'name' => 'Customer']
            ],
        ]);

        try {
            $apiInstance->sendTransacEmail($sendSmtpEmail);
            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
