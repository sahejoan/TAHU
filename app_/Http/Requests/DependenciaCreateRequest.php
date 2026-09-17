<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class DependenciaCreateRequest extends Request {

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
            'id_jefedep'  => 'required|min:1|max:4294967295|numeric',
            'id_reg'      => 'required|min:1|max:4294967295|numeric',
            'ubicacion'   => 'required|max:190',
            'nom_dep'     => 'required|max:190',
        ];
    }

    public function messages()
    {
        return [
            'id_jefedep.required'      => 'Jéfe de dependencia es requerido',
            'id_jefedep.min'           => 'Valor mínimo del jéfe de dependencia seleccionada no válido',
            'id_jefedep.max'           => 'Valor máximo del jefe de dependencia seleccionada no válido',

            'id_reg.required'      => 'Región es requerido',
            'id_reg.min'           => 'Valor mínimo de la región seleccionada no válido',
            'id_reg.max'           => 'Valor máximo de la región seleccionada no válido',

            'ubicacion.required'     => 'Ubicación de la dependencia es requerido',
            'ubicacion.max'          => 'Ubicación de la dependencia tiene más de 190 caractéres',            

            'nom_dep.required'     => 'Nombre de la dependencia es requerido',
            'nom_dep.max'          => 'Nombre de la dependencia tiene más de 190 caractéres',                        
        ];
    }  

}
?>