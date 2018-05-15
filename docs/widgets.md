# YiiTK

## Widgets

### Alert

Widget desenvolvido por [Kartik Visweswaran](mailto:kartikv2@gmail.com) e [Alexander Makarov](mailto:sam@rmcreative.ru) com o objetivo de
apresentar **Flash Messages** para o usuário.

Exemplo para adicionar na Sessão:

```php
    \Yii::$app->session->setFlash('error', 'This is the message');
    \Yii::$app->session->setFlash('success', 'This is the message');
    \Yii::$app->session->setFlash('info', 'This is the message');
    
    \Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
```

Exemplo utilizando a trait `yiitk\web\FlashMessagesTrait` (incluída em `yiitk\web\Controller`, `yiitk\base\Model` e `yiitk\db\ActiveRecord`):

```php
$this->addSuccessMessage('This is the message.'); // Key: success
$this->addErrorMessage('This is the message.'); // Key: error
$this->addWarningMessage('This is the message.'); // Key: warning
$this->addInfoMessage('This is the message.'); // Key: info
```

Exemplo de uso do Widget na View:

```php
<div class="container">
    <?= \yiitk\widgets\Alert::widget(); ?>
</div>

```

### Paginação

#### Separated Pager for Yii2

Um `Yii2 LinkPager` que apresenta as primeiras e últimas páginas juntamente com as outras páginas. Detalhes em [justinvoelker/yii2-separatedpager](https://github.com/justinvoelker/yii2-separatedpager).

Exemplo de paginação utilizando a extensão **Separated Pager for Yii2**:

![sample](https://cloud.githubusercontent.com/assets/2441889/6312491/6a89be10-b948-11e4-9ac8-bcd793664e1a.png) 

Exemplo de uso:

```php
GridView::widget([
    'dataProvider' => $dataProvider,
    'pager' => [
        'class'            => \yiitk\widgets\LinkPager::class,
        'maxButtonCount'   => 7,
        'prevPageLabel'    => 'Previous',
        'nextPageLabel'    => 'Next',
        'prevPageCssClass' => 'prev hidden-xs',
        'nextPageCssClass' => 'next hidden-xs',
        'activePageAsLink' => false,
    ]
]);

[&#171; Voltar](../README.md)
