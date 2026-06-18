<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SelfClientRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'document_number' => ['required', 'string', 'max:20', function ($attribute, $value, $fail) {
                $cleanDocument = preg_replace('/[^0-9]/', '', $value);
                if (strlen($cleanDocument) === 11) {
                    if (!$this->isValidCPF($cleanDocument)) {
                        $fail('O CPF informado é inválido.');
                    }
                } elseif (strlen($cleanDocument) === 14) {
                    if (!$this->isValidCNPJ($cleanDocument)) {
                        $fail('O CNPJ informado é inválido.');
                    }
                } else {
                    $fail('O documento deve ter 11 dígitos (CPF) ou 14 dígitos (CNPJ).');
                }
            }],
            'contributor_type' => 'required|in:1,2,9',
            'state_registration' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $cleanDocument = preg_replace('/[^0-9]/', '', $this->input('document_number'));
                    if (strlen($cleanDocument) === 14 && empty($value)) {
                        $fail('Inscrição Estadual é obrigatória para CNPJ');
                    }
                },
            ],
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'contact1' => 'nullable|string|max:255',
            'contact2' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ],
        ];

        // Se for edição (PUT/PATCH), ajusta regras
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['password'] = [
                'nullable',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols()
            ];
            $rules['email'] = [
                'required',
                'email',
                'max:255',
            ];
            // Não permite alterar documento na edição
            unset($rules['document_number']);
        }

        return $rules;
    }

    /**
     * Valida CPF
     */
    private function isValidCPF(string $cpf): bool
    {
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida CNPJ
     */
    private function isValidCNPJ(string $cnpj): bool
    {
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Primeiro dígito verificador
        $sum = 0;
        $weight = 5;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weight;
            $weight = $weight === 2 ? 9 : $weight - 1;
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        if ($cnpj[12] != $digit1) {
            return false;
        }

        // Segundo dígito verificador
        $sum = 0;
        $weight = 6;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weight;
            $weight = $weight === 2 ? 9 : $weight - 1;
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        if ($cnpj[13] != $digit2) {
            return false;
        }

        return true;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'document_number.required' => 'O documento é obrigatório.',
            'document_number.max' => 'O documento não pode ter mais de 20 caracteres.',
            'contributor_type.required' => 'O tipo de contribuinte é obrigatório.',
            'contributor_type.in' => 'Tipo de contribuinte inválido.',
            'state_registration.max' => 'A inscrição estadual não pode ter mais de 20 caracteres.',
            'phone1.required' => 'O telefone principal é obrigatório.',
            'phone1.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'phone2.max' => 'O telefone secundário não pode ter mais de 20 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
            'password.letters' => 'A senha deve conter pelo menos uma letra.',
            'password.mixed' => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve conter pelo menos um número.',
            'password.symbols' => 'A senha deve conter pelo menos um símbolo.',
        ];
    }
}
