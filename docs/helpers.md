# YiiTK

## Helpers

### yiitk\helpers\ArrayHelper

Estende a classe `\yii\helpers\ArrayHelper` e adiciona o(s) seguinte(s) método(s):

- **asAssociative(array $items)**: retorna um array como associativo

### yiitk\helpers\ConsoleHelper

Estende a classe `\yii\helpers\Console` sem adicionar nenhum método.

### yiitk\helpers\DateTimeHelper

Estende a classe `\Carbon\Carbon` sem adicionar nenhum método. Mais informações sobre a biblioteca: https://carbon.nesbot.com/.

### yiitk\helpers\FileHelper

Estende a classe `\yii\helpers\FileHelper` sem adicionar nenhum método.

### yiitk\helpers\FormatConverterHelper

Estende a classe `\yii\helpers\FormatConverter` sem adicionar nenhum método.

### yiitk\helpers\HtmlHelper

Estende a classe `\yii\helpers\Html` sem adicionar nenhum método.

### yiitk\helpers\HtmlPurifierHelper

Estende a classe `\yii\helpers\HtmlPurifier` sem adicionar nenhum método.

### yiitk\helpers\InflectorHelper

Estende a classe `\yii\helpers\Inflector` sem adicionar nenhum método.

### yiitk\helpers\JsonHelper

Estende a classe `\yii\helpers\Json` sem adicionar nenhum método.

### yiitk\helpers\MarkdownHelper

Estende a classe `\yii\helpers\Markdown` sem adicionar nenhum método.

### yiitk\helpers\MaskHelper

Estende a classe `\yiitk\helpers\StringHelper` e adiciona o(s) seguinte(s) método(s):

- **mask(string $string, string $mask, string $empty = '')**: retorna uma string formatada com a máscara informada (máscaras padrões: `cpf`, `cnpj`, `zipcode`, `credit-card`). Ex.: `MaskHelper::mask('00000000000', 'cpf', '-')` ou `MaskHelper::mask('0000', '##-##', '-')`

### yiitk\helpers\NumberHelper

Métodos disponíveis:

- **justNumbers(string $content = '')**: retorna uma string contendo apenas digitos;

### yiitk\helpers\SlugHelper

Estende a classe `\yiitk\helpers\StringHelper` e adiciona o(s) seguinte(s) método(s):

- **asSlugs(array $items, string $method = SlugHelper::SLUG_METHOD_SINGLE)**: converte o conteúdo de um `array` em `slugs` 
- **convert($value = '', $spaces = '-', $case = MB_CASE_LOWER)**: converte uma `string` em `slug`

### yiitk\helpers\StringHelper

- **justNumbers(string $content = '')**: retorna a `string` com apenas digitos
- **justLetters(string $content = '')**: retorna a `string` com apenas letras
- **compare(string $originalValue = '', string $targetValue = '')**: compara dois valores retornando `true` ou `false`
- **asSlug(string $value = '', string $spaces = '-', string $case = MB_CASE_LOWER)**: retorna uma `string` como `slug`
- **convertCase(string $string, string $mode = StringHelper::CASE_UPPER)**: converte uma `string` em minúscula, maiúscula ou como título
- **toLowerCase(string $string)**: converte uma `string` para minúscula
- **toUpperCase(string $string)**: converte uma `string` para maiúscula
- **generateRandomString(integer $length = 0, integer $upper = 0, integer $lower = 0, integer $digit = 0, integer $special = 0)**: gera uma `string` randômica
- **obfuscateEmail(string $email)**: ofusca um endereço de e-mail
- **removeAccents(string $string)**: remove os acentos de uma `string`

### yiitk\helpers\UrlHelper

Estende a classe `\yii\helpers\Url` sem adicionar nenhum método.

### yiitk\helpers\VarDumperHelper

Estende a classe `\yii\helpers\VarDumper` sem adicionar nenhum método.

[&#171; Voltar](../README.md)
