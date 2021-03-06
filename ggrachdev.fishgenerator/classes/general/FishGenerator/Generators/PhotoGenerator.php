<?

namespace GGrach\FishGenerator\Generators;

use GGrach\FishGenerator\Exceptions\GeneratePhotoException;
use GGrach\FishGenerator\Debug\Debug;

/**
 * @todo Add https://dummyimage.com/
 * @todo https://loremflickr.com/ 
 * @todo https://loremipsum.io/ru/21-of-the-best-placeholder-image-generators/
 */

/**
 * Все, что связано с генерацией фотографий
 */
class PhotoGenerator extends Debug {
    /*
     * Категории фотографи которые можно установить для генерации
     */

    const VALID_CATEGORIES_PHOTO = [
        'abstract', 'animals', 'business',
        'cats', 'city', 'food',
        'fashion', 'people', 'nature',
        'sports', 'technics', 'transport'
    ];

    // @var string|array
    protected $categoryPhoto = null;

    /**
     * Установить категорию фото
     * 
     * @param string|array $categoryPhoto
     * @return \GGrach\Generators\ElementFishGenerator
     */
    public function setCategoryPhoto($categoryPhoto): self {

        $isValidPhotoCategory = false;

        if (is_string($categoryPhoto)) {
            if (in_array($categoryPhoto, static::VALID_CATEGORIES_PHOTO)) {
                $this->categoryPhoto = $categoryPhoto;
                $isValidPhotoCategory = true;
            }
        } else if (is_array($categoryPhoto)) {
            $arCorrectCategories = array_intersect(static::VALID_CATEGORIES_PHOTO, $categoryPhoto);

            if (!empty($arCorrectCategories)) {
                $this->categoryPhoto = array_unique($arCorrectCategories);
                $isValidPhotoCategory = true;
                sort($this->categoryPhoto);
            }
        }

        if ($isValidPhotoCategory === false) {
            if ($this->isStrictMode) {
                if (is_array($categoryPhoto)) {
                    throw new GeneratePhotoException('Not found photos with categories ' . implode(',', $categoryPhoto));
                } else {
                    throw new GeneratePhotoException('Not found photos with category ' . $categoryPhoto);
                }
            }
            if (is_array($categoryPhoto)) {
                $this->addError('Not found photos with categories ' . implode(',', $categoryPhoto));
            } else {
                $this->addError('Not found photos with category ' . $categoryPhoto);
            }
        }

        return $this;
    }

    public function generatePhotoFromLink(string $photoLink): array {
        
        // Получаем итоговую ссылку даже если есть редирект
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $photoLink);
        \curl_setopt($ch, CURLOPT_HEADER, true);
        \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        \curl_exec($ch);
        if(\curl_errno($ch))
        {
            throw new GeneratePhotoException(\curl_error($ch));
        }

        $url = \curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        \curl_close($ch);
        
        
        $pictureArray = \CFile::MakeFileArray($url);
        if (empty($pictureArray['tmp_name'])) {

            $error = 'Error save image from link ' . $photoLink . ', maybe, can not available site generator';

            $this->addError($error);

            if ($this->isStrictMode) {
                throw new GeneratePhotoException($error);
            }

            $pictureArray = [];
        } else {
            $pictureArray['name'] = $pictureArray['name'] . '.jpg';
        }

        return $pictureArray;
    }

    public function getRandomCategoryPhoto(): string {
        $categoryPhoto = null;

        if ($this->categoryPhoto == null) {
            $categoryPhoto = static::VALID_CATEGORIES_PHOTO[array_rand(static::VALID_CATEGORIES_PHOTO, 1)];
        } else {
            if (is_array($this->categoryPhoto)) {
                $categoryPhoto = $this->categoryPhoto[array_rand($this->categoryPhoto, 1)];
            } else {
                $categoryPhoto = $this->categoryPhoto;
            }
        }

        return $categoryPhoto;
    }

}
