<?php

namespace Umanit\FrontBundle\Command;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class InitFrontCommand
 */
class InitFrontCommand extends Command
{
    /** @var string */
    const FRONT_KIT_URL = 'https://github.com/umanit/front-kit/archive/master.zip';

    /** @var string */
    protected static $defaultName = 'umanit:front-bundle:init';

    /** @var FileLocatorInterface */
    private $fileLocator;

    /** @var string */
    private $projectDir;

    /** @var array */
    private $frontKitEntries;

    /** @var array */
    private $frontBundleEntries;

    /**
     * UmanitFrontExtension constructor.
     *
     * @param FileLocatorInterface $fileLocator
     * @param string               $projectDir
     * @param array                $frontKitEntries
     * @param array                $frontBundleEntries
     */
    public function __construct(
        FileLocatorInterface $fileLocator,
        string $projectDir,
        array $frontKitEntries,
        array $frontBundleEntries
    ) {
        $this->projectDir = $projectDir;
        $this->fileLocator = $fileLocator;
        $this->frontKitEntries = $frontKitEntries;
        $this->frontBundleEntries = $frontBundleEntries;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Initialise le front-kit.')
            ->setHelp('Cette commande va initialiser le front-kit dans le projet.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Téléchargement du front-kit...</comment>');

        /** @var string $bundleResources */
        $bundleResources = $this->fileLocator->locate('@UmanitFrontBundle/Resources');
        $frontKitPath = $this->downloadFile(self::FRONT_KIT_URL);

        if (false === $frontKitPath || !is_file($frontKitPath)) {
            $output->writeln('<error>Erreur lors du téléchargement du front-kit.</error>');

            return 1;
        }

        $output->writeln('<comment>Extraction de l\'archive...</comment>');

        $zip = new \ZipArchive();

        if (true !== $zip->open($frontKitPath)) {
            $output->writeln('<error>Erreur lors de l\'ouverture de l\'archive.</error>');
        }

        $extractedFrontKitPath = $frontKitPath.'_extract';

        if (!mkdir($extractedFrontKitPath, 0700) && !is_dir($extractedFrontKitPath)) {
            $output->writeln('<error>Erreur lors de l\'extraction de l\'archive.</error>');

            return 1;
        }

        if (!$zip->extractTo($extractedFrontKitPath)) {
            $output->writeln('<error>Erreur lors de l\'extraction de l\'archive.</error>');

            return 1;
        }

        $zip->close();

        $filesystem = new Filesystem();

        $output->writeln('<comment>Suppression des fichiers de webpack-encore...</comment>');
        $filesystem->remove([$this->projectDir.'/assets/js/app.js', $this->projectDir.'/assets/css/app.css']);

        $cssDir = $this->projectDir.'/assets/css';

        if (is_dir($cssDir)) {
            $fileIterator = new \FilesystemIterator($cssDir, \FilesystemIterator::SKIP_DOTS);

            if (0 === iterator_count($fileIterator)) {
                $filesystem->remove($this->projectDir.'/assets');
            }
        }

        $output->writeln('<comment>Copie des fichiers de front-kit et front-bundle...</comment>');

        $dataset = [
            $extractedFrontKitPath.'/front-kit-master/' => $this->frontKitEntries,
            $bundleResources                            => $this->frontBundleEntries,
        ];

        foreach ($dataset as $baseSource => $entries) {
            foreach ($entries as $entry) {
                $target = $this->projectDir.'/'.$entry;

                // On n'écrase pas un fichier déjà existant.
                if (is_file($target)) {
                    $output->writeln(sprintf(
                        '<error>Impossible de copier %s car il existe déjà dans le projet.</error>',
                        $entry
                    ));

                    continue;
                }

                $source = $baseSource.'/'.$entry;

                if (is_file($source)) {
                    $filesystem->copy($source, $target);
                } elseif (is_dir($source)) {
                    $filesystem->mirror($source, $target);
                }
            }
        }

        $output->writeln('<comment>Nettoyage des éléments temporaires...</comment>');

        $filesystem->remove([$frontKitPath, $extractedFrontKitPath]);

        $webpackConfigFile = $this->projectDir.'/webpack.config.js';

        if (is_file($webpackConfigFile)) {
            $output->writeln('<comment>Modification du <options=bold>webpack.config.js</>...</comment>');

            $symfonyConfig = file_get_contents($bundleResources.'/symfony.config.js');

            if (false === $symfonyConfig) {
                $output->writeln('<error>Erreur lors de la lecture du fichier <options=bold>symfony.config.js</>.</error>');

                return 1;
            }

            $webpackConfig = file_get_contents($this->projectDir.'/webpack.config.js');

            if (false === $webpackConfig) {
                $output->writeln('<error>Erreur lors de la lecture du fichier <options=bold>webpack.config.js</>.</error>');

                return 1;
            }

            $matches = [];

            preg_match_all('/###> (?\'part\'\w+) ###\n(?\'content\'.*)?###< \1 ###/s', $symfonyConfig, $matches);

            $parts = array_combine($matches['part'], $matches['content']);

            if (false === $parts) {
                $output->writeln('<error>Erreur lors de l\'analyse du fichier <options=bold>symfony.config.js</>.</error>');

                return 1;
            }

            if (array_key_exists('imports', $parts)) {
                $webpackConfig = $parts['imports'].$webpackConfig;
            }

            if (array_key_exists('config', $parts)) {
                $webpackConfig = str_replace(
                    'module.exports = Encore.getWebpackConfig();',
                    $parts['config'].PHP_EOL.'module.exports = Encore.getWebpackConfig();',
                    $webpackConfig
                );
            }

            if (false === file_put_contents($webpackConfigFile, $webpackConfig)) {
                $output->writeln('<error>Erreur lors de la fusion de <options=bold>symfony.config.js</> dans <options=bold>webpack.config.js</>.</error>');
            }
        } else {
            $output->writeln('<error>Aucun fichier <options=bold>webpack.config.js</> trouvé.</error>');
        }

        $output->writeln('<info>Il ne vous reste plus qu\'à exécuter les commandes <options=bold>nvm exec yarn install</> et <options=bold>nvm exec yarn dev</>.</info>');

        return 0;
    }

    /**
     * @param string $url
     *
     * @return string|false
     */
    private function downloadFile(string $url)
    {
        $newfname = $this->getTemp();

        if (!$newfname) {
            return $newfname;
        }

        $file = fopen($url, 'rb');

        if ($file) {
            $newf = fopen($newfname, 'wb');

            if ($newf) {
                while (!feof($file)) {
                    $str = fread($file, 1024 * 8);

                    if (false === $str) {
                        throw new \OutOfBoundsException('Impossible de lire le stream.');
                    }

                    fwrite($newf, $str, 1024 * 8);
                }

                fclose($newf);
            }

            fclose($file);
        }

        return $newfname;
    }

    /**
     * @return false|string
     */
    private function getTemp()
    {
        return tempnam(sys_get_temp_dir(), 'umanit_front_');
    }
}
