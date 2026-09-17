<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class PeriodoRequest extends Request {

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
	 * @return array
	 */
    public function rules()
    {
        return [
            'desde' => 'date_format:Y-m-d',
            'hasta' =>  'date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'desde.date_format'=> 'Fecha de inicio inválida',
            'hasta.date_format'=> 'Fecha de final inválida',
        ];
    }  

}
?>