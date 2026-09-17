<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class FirmasUpdateRequest extends Request {

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
            'firma'     => 'required|max:190',
            'id_dep'      => 'required|min:1|max:4294967295|numeric',
            'actual'    => 'min:0|max:1|numeric',            
            'provi_jefe'  => 'required|max:20',
            'fecha_provi' => 'required|date_format:Y-m-d',
            'gaceta_provi' => 'required|max:4294967295|numeric',
            'fecha_gaceta' => 'required|date_format:Y-m-d',            
        ];
    }

    public function messages()
    {
        return [
            'firma.required'     => 'Nombre del firmante es requerido',
            'firma.max'          => 'Nombre del firmante tiene más de 190 caractéres',

            'id_dep.required'      => 'Ubicación es requerido',
            'id_dep.min'           => 'Valor mínimo de la ubicación seleccionada no válido',
            'id_dep.max'           => 'Valor máximo de la ubicacion seleccionada no válido',

            'actual.min'           => 'Valor mínimo de estatus inválido',
            'actual.max'           => 'Valor máximo de estatus inválido',

            'provi_jefe.required'  => 'Número de la providencia es requerido',
            'provi_jefe.max'       =>  'Número de la providencia tiene más de 20 caractéres',

            'fecha_provi.required'    => 'Fecha de la providencia es requerido.',
            'fecha_provi.date_format' => 'Fecha de la providencia tiene formato inválido.',

            'gaceta_provi.required' => 'Número de la gaceta es requerido',
            'gaceta_provi.max    '  => 'Número de la gaceta ni tiene valor válido',        
            'gaceta_provi.numeric' => 'Número de la gaceta ni tiene formato válido',

            'fecha_gaceta.required'    => 'Fecha de la gaceta es requerido.',
            'fecha_gaceta.date_format' => 'Fecha de la gaceta tiene formato inválido.',            
        ];
    }  

}
?>