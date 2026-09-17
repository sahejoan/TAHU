<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class AreaCreateRequest extends Request {

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
            'nom_area'     => 'required|max:190',
            'ubicacion'    => 'required|max:190',
            'telf_area'    => 'max:15',
            'coord_area'  => 'required|max:190',
            'id_divi'     => 'required|min:1|max:4294967295|numeric',            
        ];
    }

    public function messages()
    {
        return [
            'nom_area.required'     => 'Nombre del área es requerido',
            'nom_area.max'          => 'Nombre del área tiene más de 190 caractéres',

            'ubicacion.required'   => 'Ubicación de la región es requerido',
            'ubicacion.max'        => 'ubicación tiene más de 190 caractéres',

            'telf_area.required'    => 'Teléfono de la región es requerido',
            'telf_area.max'         => 'teléfono tiene más de 15 caractéres',

            'coord_area.required'  => 'Coordenadas es requerido',
            'coord_area.max'       => 'Coordenadas tiene más de 190 caractéres',

            'id_divi.required'      => 'División es requerido',
            'id_divi.min'           => 'Valor mínimo de la divisón seleccionada no válido',
            'id_divi.max'           => 'Valor máximo de la divisón seleccionada no válido',                        
        ];
    }  

}
?>