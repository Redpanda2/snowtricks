<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\TrickImage;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageService
{
    private LoggerInterface $logger;
    private ParameterBagInterface $params;
    private SluggerInterface $slugger;
    private Filesystem $filesystem;
    private string $imageNewName;
    private $targetTrickImageDirectory;
    private $targetAvatarDirectory;

    public function __construct($targetTrickImageDirectory, $targetAvatarDirectory, LoggerInterface $logger, ParameterBagInterface $params, SluggerInterface $slugger, Filesystem $filesystem)
    {
        $this->logger = $logger;
        $this->params = $params;
        $this->slugger = $slugger;
        $this->filesystem = $filesystem;
        $this->targetTrickImageDirectory = $targetTrickImageDirectory;
        $this->targetAvatarDirectory = $targetAvatarDirectory;
    }

//    public function moveImageToFinalDirectory(UploadedFile $file)
//    {
//        $newFilename = $this->generateNewFileName($file);
//        try {
//            $file->move(
//                $this->getTargetDirectory(),
//                $newFilename
//            );
//        } catch (FileException $e) {
//            // ... handle exception if something happens during file upload
//            $this->logger->error('failed to upload image: ' . $e->getMessage());
//            throw new FileException('Il y a eu un probleme lors de l\'envoi d\'un fichier');
//        }
//    }

    public function moveTrickImageToFinalDirectory(TrickImage $trickImage): string
    {
        $file = $trickImage->getFile();
        $newFilename = $this->generateNewFileName($file);
        $trickImage->setFilename($newFilename);
        try {
            $file->move(
                $this->getTargetTrickImageDirectory(),
                $newFilename
            );
            return $newFilename;
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            $this->logger->error('failed to upload image: ' . $e->getMessage());
            throw new FileException('Il y a eu un probleme lors de l\'envoi d\'un fichier');
        }
    }

    public function removeUploadedTrickImage(TrickImage $trickImage): void
    {
        try {
            $fileLocation = $trickImage->getPath();

            if($this->filesystem->exists($fileLocation))
            {
                unlink($fileLocation);
            }
        } catch (FileException $exception){
            $this->logger->error('failed to remove image: ' . $exception->getMessage());
            throw new FileException('Il y a eu un probleme lors de la suppression du fichier');
        }
    }

    public function uploadAvatar(UploadedFile $uploadedFile): string
    {
        $newFilename = $this->generateNewFileName($uploadedFile);
        try {
            $uploadedFile->move(
                $this->getTargetAvatarDirectory(),
                $newFilename
            );
            return $newFilename;
        } catch (FileException $e) {
            return throw new FileException('Il y a eu un probleme lors de l\'envoi d\'un fichier');
        }

    }

    private function generateNewFileName(File $file): string
    {
        if($file instanceof UploadedFile)
        {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug(pathinfo($originalFilename, PATHINFO_FILENAME));
        $this->imageNewName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        return $this->imageNewName;
    }

    public function getImageNewName(): string
    {
        return $this->imageNewName;
    }

    public function getTargetTrickImageDirectory()
    {
        return $this->targetTrickImageDirectory;
    }
    public function getTargetAvatarDirectory()
    {
        return $this->targetAvatarDirectory;
    }
//
//    public function getPublicPath(string $path): string
//    {
//        return 'uploads/'.$path;
//    }

//
//    protected function getUploadRootDir()
//    {
//        // On retourne le chemin relatif vers l'image
//        return __DIR__.'/../../../../web/'.$this->getUploadDir();
//    }
//
//    public function getUrl()
//    {
//        return $this->id.'.'.$this->extension;
//    }
}
