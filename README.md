# Kraken.io Magento Extension

**Advanced optimization for your Magento JPEG, PNG, GIF and SVG images**

Established in 2012, Kraken.io is an industry-leading image optimizer like no other capable of significantly reducing the file size of popular image formats using tried-and-tested optimization techniques developed with a single goal in mind: To reduce their file size by as much as possible while retaining the image quality.

**Kraken.io is trusted by thousands of customers worldwide, from individuals to small/medium sized businesses and even Fortune 500 companies.**

## How does it work?

When a product is viewed for the first time, Magento will create different image sizes and put those sizes into its cache. With the Kraken.io extension installed, those images first get optimized by our API before being pushed into the cache folder.

Other than enabling the Kraken.io extension, entering your API credentials and verifying your settings, no further action is required. The optimization will happen on demand, and in the background, each time new image cache entries are generated.

Kraken.io supports **both lossless and intelligent lossy optimization modes**. Both sRGB and CMYK colorspaces are supported. All JPEG images are re-encoded as progressive. Alpha transparency is maintained for PNG images, and animated GIFs remain animated after optimization.

Other features include:

- The ability to optimize your Skins and Media folders in a single click.
- The ability to define a discreet quality (1-100) for your JPEG images (when using the Advanced mode).

## Quick Start

You can register for your FREE Kraken.io account [here](https://kraken.io/signup). Once you have verified your account, you may log in and obtain your API credentials by choosing the API Credentials menu item found on the left of your [Account Overview](https://kraken.io/account/api-credentials). Your FREE account comes with 50MB of testing quota to allow you to test our extension with your platform, prior to upgrading to one of our affordable, transparently priced [paid plans](https://kraken.io/plans).

## Installation

1. Use Magento Connect Manager to install the [Kraken.io Image Optimizer extension](http://www.magentocommerce.com/magento-connect/catalog/product/view/id/29332/). Alternatively you can copy files from this repository to your Magento root folder.
2. Find the Kraken.io menu option in the top menu and choose "Kraken API configuration".
3. Enter your Kraken.io API key and secret. As soon as that is done, be sure to click on "Save Config" at the top right of the screen.

## Usage

1. Once your credentials have been saved, the extension is ready to be used. You can specify whether or not to keep backups of your original images, the compression mode setting and other available options. We recommend sticking with the default settings.
2. From now on, every newly uploaded image will have cache entries which are optimized by Kraken.io, when the product page is viewed for the first time.
3. To optimize the already-existing images on your platform, first choose "Image Optimizer" from the Kraken.io menu. From there, click on "Flush Catalog Images Cache". You should only have to perform this once upon initial installation of the Kraken.io Image Optimizer extension.
4. From the same screen, you may also optimize your Skins and Media folders, by clicking on their respective buttons.
5. You can view your statistics by choosing "Optimization Statistics" from the Kraken.io menu at the top.

## Benefits of image optimization

In nutshell, faster loading sites are viewed more favourably than slower loading sites by both users and search engines. Getting your site to load faster can go a long way to improving your business's bottom line. For more information, visit our [Support Centre](https://support.kraken.io/general/how-can-krakenio-help-to-boost-my-websites-search-ranking)

Got a question? Need some help? Get in touch with [Kraken.io support](https://support.kraken.io), which is easily visible when logged in to Kraken.io.

## License

Kraken.io Magento Extension is distributed under the GNU GENERAL PUBLIC LICENSE V2. Please read the full license [here](https://github.com/kraken-io/kraken-magento/blob/master/LICENSE).