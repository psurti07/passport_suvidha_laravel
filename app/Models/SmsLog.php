<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'type',
        'crontype',
        'cronname',
        'msgcount',
        'msgresponse'
    ];

    public function getMsgresponseAttribute($value)
    {
        $data = json_decode($value, true);

        if (!$data || !isset($data['response'])) {
            return $value;
        }

        $response = $data['response'];

        if (str_contains($response, '<smslist>')) {

            libxml_use_internal_errors(true);

            $xml = simplexml_load_string(trim($response));

            if ($xml && isset($xml->sms)) {

                $messages = [];

                foreach ($xml->sms as $sms) {

                    $messages[] =
                        'success ' .
                        (string)$sms->code . ' ' .
                        (string)$sms->reason . ' ' .
                        (string)$sms->clientsmsid . ' ' .
                        (string)$sms->messageid . ' ' .
                        (string)$sms->mobileno;
                }

                return json_encode([
                    'success' => $data['success'],
                    'response' => implode(' ', $messages)
                ]);
            }
        }

        return $value;
    }
}
