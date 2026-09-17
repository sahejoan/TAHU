<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class RegionCreateRequest extends Request {

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
            'nom_reg'     => 'required|max:190',
            'direccion'   => 'required|max:190',
            'telf_reg'    => 'max:15',
            'coordenadas' => 'required|max:190'
        ];
    }

    public function messages()
    {
        return [
            'nom_reg.required'     => 'Nombre de la región es requerido',
            'nom_reg.max'          => 'Nombre tiene más de 190 caractéres',

            'direccion.required'   => 'Dirección de la región es requerido',
            'direccion.max'        => 'Dirección tiene más de 190 caractéres',

            'telf_reg.required'    => 'Teléfono de la región es requerido',
            'telf_reg.max'         => 'teléfono tiene más de 15 caractéres',

            'coordenadas.required'  => 'Coordenadas es requerido',
            'coordenafas.max'       => 'Coordenadas tiene más de 190 caractéres',
        ];
    }  

}
?>