# 🌟 FastSitePHP Playground

**Thanks for visiting!** 🌠👍

* __Playground UI__: https://www.fastsitephp.com/en/playground
* __Playground Server__: https://playground.fastsitephp.com

<table>
  <tbody>
    <tr>
      <td>en</td>
      <td>English</td>
      <td>This repository contains playground website for FastSitePHP. The UI (User Interface) exists on the main website in the main FastSitePHP repository, while this repository only contains code that exists on the separate playground web server.</td>
    </tr>
    <tr>
      <td>ja</td>
      <td lang="zn-CH">中文 (简体)</td>
      <td lang="zn-CH">该存储库包含FastSitePHP的游乐场网站。 UI（用户界面）位于主FastSitePHP存储库中的主网站上，而此存储库仅包含存在于单独的Playground Web服务器上的代码。</td>
    </tr>
    <tr>
      <td>es</td>
      <td lang="es">Español</td>
      <td lang="es">Este repositorio contiene un sitio web de juegos para FastSitePHP. La interfaz de usuario (UI) existe en el sitio web principal en el repositorio principal FastSitePHP, mientras que este repositorio solo contiene el código que existe en el servidor web de juegos independiente.</td>
    </tr>
    <tr>
      <td>pt-BR</td>
      <td lang="pt-BR">Português (do Brasil)</td>
      <td lang="pt-BR">Este repositório contém site de playground para FastSitePHP. A interface do usuário (Interface do usuário) existe no site principal no repositório principal do FastSitePHP, enquanto esse repositório contém apenas código que existe no servidor da Web de playground separado.</td>
    </tr>
    <!--
    <tr>
      <td>{iso}</td>
      <td>{lang}</td>
      <td>{content}</td>
    </tr>
    -->
  </tbody>
</table>

## :desktop_computer: Running Locally

Download this repository then run the install script. This will also generate a new `app_data/.env` file which is used for authentication.

~~~
cd {root-directory}
php ./scripts/install.php
~~~

Or to install using Composer: `composer require fastsitephp/fastsitephp`. Then copy `app_data/.env.example` to `app_data/.env`.

Then follow instructions in the root `index.php` page to run the site. You will also need to point the UI from the local build of the main site to the local playground server. Search for “urlRoot:” in the `fastsitephp\website\public\js\playground.js` file and make the change.

https://github.com/fastsitephp/fastsitephp/blob/master/website/public/js/playground.js

## :gear: How it works

<p align="center">
    <img src="https://github.com/fastsitephp/static-files/blob/master/img/playground/How-it-Works.svg" alt="Playground - How it works">
</p>

## :gear: Detailed Server Setup

https://github.com/fastsitephp/playground/blob/master/docs/Playground%20Server%20Setup.txt

## :handshake: Contributing

* If you find a typo or grammar error please fix and submit.
* Additional language template translations are needed. Refer to the main project if you can help with translations.
* Any changes to the core code will likely not be accepted unless you first open an issue. A lot of security is needed in order to make this site work so every line of code must be carefully considered.
* If you think you’ve found an issue with security or have additional security ideas please open an issue. No financial transactions other than the cost of the server are dependent on this site so opening a public issue is ok. However if you are able to obtain root or sudo access to the server please [get in touch privately](https://www.fastsitephp.com/en/security-issue).

## :memo: License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.
