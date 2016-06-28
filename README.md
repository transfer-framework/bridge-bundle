Transfer Bridge Bundle
======================

Installation
------------

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

    $ composer require transfer/bridge-bundle

This requires [Composer](https://getcomposer.org/download/) to be installed globally in your system.

Enable the Bundle
-----------------

Then, enable the bundle by adding the following line in the app/AppKernel.php file of your project:

    // app/AppKernel.php
    class AppKernel extends Kernel
    {
      public function registerBundles()
      {
          $bundles = array(
              // ...
              new Bridge\Bundle\BridgeBundle(),
          );

          // ...
      }
    }

License
-------

This bundle is under the MIT license. See the complete license in the bundle.
