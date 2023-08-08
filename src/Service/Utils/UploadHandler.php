<?php

namespace App\Service\Utils;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadHandler
{
    /** @var array|string[]  */
    private array $allowedMimeTypes = [
        "application/pdf",
        "image/jpeg",
        "image/jpg",
        "image/gif",
        "image/png",
        "image/svg+xml",
        "image/svg",
        "image/webp"
    ];

    private SluggerInterface $slugger;
    private string $filesDirectory;
    private string $filesWebPath;

    public function __construct(SluggerInterface $slugger, string $filesDirectory, string $filesWebPath)
    {
        $this->slugger = $slugger;
        $this->filesDirectory = $filesDirectory;
        $this->filesWebPath = $filesWebPath;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $path
     * @param string|null $croppedFile
     * @return string
     * @throws \Exception
     */
    private function moveFile(UploadedFile $uploadedFile, string $path, ?string $croppedFile): string
    {
        if (!in_array($uploadedFile->getMimeType(), $this->allowedMimeTypes)) {
            throw new \Exception('Type de fichier non autorisé.', 415);
        }
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        if ($croppedFile !== null && $croppedFile !== "") {
            $crop = fopen($path . $newFilename, "wb");
            if ($crop !== false) {
                $data = explode(',', $croppedFile);
                if (array_key_exists(1, $data)) {
                    fwrite($crop, base64_decode($data[1]));
                } else {
                    fwrite($crop, base64_decode($data[0]));
                }
                fclose($crop);
            } else {
                throw new \Exception("Une erreur est survenue lors du traitement de l'image cropper.", 500);
            }
        } else {
            try {
                $uploadedFile->move(
                    $path,
                    $newFilename
                );
            } catch (FileException $err) {
                throw new \Exception('Une erreur est survenue lors de l\'upload de ce fichier.', 500);
            }
        }

        return $newFilename;
    }

    /**
     * @param UploadedFile $file
     * @param null $croppedFile
     * @return string
     * @throws \Exception
     */
    public function uploadFile(UploadedFile $file, $croppedFile = null): string
    {
        return $this->filesWebPath . $this->moveFile($file, $this->filesDirectory, $croppedFile);
    }
}