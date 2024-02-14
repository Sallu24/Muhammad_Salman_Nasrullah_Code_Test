<?php

namespace DTApi\Http\Requests;

use DTApi\Helpers\TeHelper;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'from_language_id' => 'sometimes',
            'due_date' => 'sometimes',
            'due_time' => 'sometimes',
            'customer_phone_type' => 'sometimes',
            'duration' => 'sometimes',
            'custom_user_type' => 'required|in:' . getCustomerRoleId(),


//            'due_date' => 'required_if:immediate,no',
        ];
    }

    protected function prepareForValidation()
    {
        $user = $this->__authenticatedUser;
        $immediatetime = 5;
        $consumerType = $user->userMeta->consumer_type;

        if ($user->user_type == getCustomerRoleId()) {
            $this->merge([
                'custom_user_type' => getCustomerRoleId()
            ]);

            if (isset($this->customer_phone_type)) {
                $this->merge([
                    'customer_phone_type' => 'yes'
                ]);
            } else {
                $this->merge([
                    'customer_phone_type' => 'no'
                ]);
            }

            if (isset($this->customer_physical_type)) {
                $this->merge([
                    'customer_physical_type' => 'yes'
                ]);
            } else {
                $this->merge([
                    'customer_physical_type' => 'no'
                ]);
            }

            if (in_array('male', $this->job_for)) {
                $this->gender = 'male';
            } elseif (in_array('female', $this->job_for)) {
                $this->gender = 'female';
            } elseif (in_array('normal', $this->job_for)) {
                if (in_array('certified', $this->job_for)) {
                    $this->certified = 'both';
                } elseif (in_array('certified_in_law', $this->job_for)) {
                    $this->certified = 'n_law';
                } elseif (in_array('certified_in_helth', $this->job_for)) {
                    $this->certified = 'n_health';
                } else {
                    $this->certified = 'normal';
                }
            } elseif (in_array('certified', $this->job_for)) {
                $this->certified = 'yes';
            } elseif (in_array('certified_in_law', $this->job_for)) {
                $this->certified = 'law';
            } elseif (in_array('certified_in_helth', $this->job_for)) {
                $this->certified = 'health';
            }

            $this->b_created_at = date('Y-m-d H:i:s');

            if ($consumerType == 'rwsconsumer') {
                $this->job_type = 'rws';
            } elseif ($consumerType == 'ngo') {
                $this->job_type = 'unpaid';
            } elseif ($consumerType == 'paid') {
                $this->job_type = 'paid';
            }

            if ($this->immediate == 'yes') {
                $due_carbon = Carbon::now()->addMinute($immediatetime);
                $this->due = $due_carbon->format('Y-m-d H:i:s');
                $this->immediate = 'yes';
                $this->customer_phone_type = 'yes';
//                $response['type'] = 'immediate';

            } else {
                $due = $this->due_date . " " . $this->due_time;
//                $response['type'] = 'regular';
                $due_carbon = Carbon::createFromFormat('m/d/Y H:i', $due);
                $this->due = $due_carbon->format('Y-m-d H:i:s');

//                if ($due_carbon->isPast()) {
//                    $response['status'] = 'fail';
//                    $response['message'] = "Can't create booking in past";
//                    return $response;
//                }

                if (isset($due))
                    $data['will_expire_at'] = TeHelper::willExpireAt($due, $data['b_created_at']);

                $this->by_admin = isset($data['by_admin']) ? $data['by_admin'] : 'no';
            }
        } else {
            $this->merge([
                'custom_user_type' => ''
            ]);
        }
    }

    public function messages()
    {
        $response['status'] = 'fail';

        return [
            'from_language_id.sometimes' => 'Du måste fylla in alla fält',
            'due_date.sometimes' => 'Du måste fylla in alla fält',
            'due_time.sometimes' => 'Du måste fylla in alla fält',
            'customer_phone_type.sometimes' => 'Du måste göra ett val här',
            'duration.sometimes' => 'Du måste fylla in alla fält',
            'custom_user_type.required' => 'Translator can not create booking',
        ];
    }
}
