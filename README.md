# 🌟 FastSitePHP Playground

**Thanks for visiting!** 🌠👍

* __Playground UI__: https://www.fastsitephp.com/en/playground
* __Playground Server__: https://playground.fastsitephp.com

<table>
  <tbody>
    <tr>
      <td><strong>en - English</strong><br> This repository contains playground website for FastSitePHP. The UI (User Interface) exists on the main website in the main FastSitePHP repository, while this repository only contains code that exists on the separate playground web server.</td>
    </tr>
    <tr>
      <td lang="zn-CH"><strong>zh-CN - 中文 (简体)</strong><br> 该存储库包含FastSitePHP的游乐场网站。 UI（用户界面）位于主FastSitePHP存储库中的主网站上，而此存储库仅包含存在于单独的Playground Web服务器上的代码。</td>
    </tr>
    <tr>
      <td lang="es"><strong>es - Español</strong><br> Este repositorio contiene un sitio web de juegos para FastSitePHP. La interfaz de usuario (UI) existe en el sitio web principal en el repositorio principal FastSitePHP, mientras que este repositorio solo contiene el código que existe en el servidor web de juegos independiente.</td>
    </tr>
    <tr>
      <td lang="pt-BR"><strong>pt-BR - Português (do Brasil)</strong><br> Este repositório contém site de playground para FastSitePHP. A interface do usuário (Interface do usuário) existe no site principal no repositório principal do FastSitePHP, enquanto esse repositório contém apenas código que existe no servidor da Web de playground separado.</td>
    </tr>
    <!--
    <tr>
      <td lang="{iso}"><strong>{iso} - {lang}</strong><br> {content}</td>
    </tr>
    -->
  </tbody>
</table>

## ⚙️ How it works

<p align="center">
    <img src="https://github.com/fastsitephp/static-files/blob/master/img/playground/How-it-Works.svg" alt="Playground - How it works">
</p>

**Update - December 30th, 2021**

Originally this site was hosted on a separate server for several years but now the playground is hosted on the same server as the main site along with several other open source sites. This site and the other sites do not get enough traffic to justify the need for separate servers so now only 1 server is used. See detailed setup docs for more. Logic from the above graphic still applies except the separate server.

## ⚙️ Detailed Server Setup

https://github.com/fastsitephp/playground/blob/master/docs/playground-server-setup.sh

## 🖥️ Running Locally

Download this repository then run the install script. This will also generate a new `app_data/.env` file which is used for authentication.

~~~
cd {root-directory}
php ./scripts/install.php
~~~

Or to install using Composer: `composer require fastsitephp/fastsitephp`. Then copy `app_data/.env.example` to `app_data/.env`.

Then follow instructions in the root `index.php` page to run the site. You will also need to point the UI from the local build of the main site to the local playground server. Search for “urlRoot:” in the `fastsitephp\website\public\js\playground.js` file and make the change.

https://github.com/fastsitephp/fastsitephp/blob/master/website/public/js/playground.js

When you run locally on a standard build of PHP user sites will be insecure however this is acceptable for local development.

## 🤝 Contributing

* If you find a typo or grammar error please fix and submit.
* Additional language template translations are needed. Refer to the main project if you can help with translations.
* Any changes to the core code will likely not be accepted unless you first open an issue. A lot of security is needed in order to make this site work so every line of code must be carefully considered.

## 🔒 Security

* The actual site uses the following disclaimer `Please do not attack this site or use it for malicious purposes`; however if you are a security researcher its understandable that you may want to test the security of this site.
* Reasonable testing is acceptable however if the site ends up being compromised maliciously or attacks slow down the main sites on the server then the playground may be taken down so please keep that in mind.
* For manual testing and details on what would be a good starting point to attack the site see files:
  * https://github.com/fastsitephp/playground/blob/master/scripts/app-error-testing.php
  * https://github.com/fastsitephp/playground/blob/master/scripts/app-error-testing-2.php
* If you think you’ve found an issue with security or have additional security ideas, please open an issue on GitHub. This site has a niche audience, nothing secure on the server, and nothing financially depends on it other than the cost of the server ($5 USD a month). Because of this, opening a public issue is ok even if you have an exploit. If you feel the exploit is better to discuss privately you can get in touch at: https://www.fastsitephp.com/en/security-issue
* If you accidentally cause serious problems to the server or take it down the please get in contact with the author immediately; so that a new server can be setup. If someone takes the server down from the playground I would be more interested in how it was done and if it can be prevented rather than worried about the server itself.

## 📝 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.
