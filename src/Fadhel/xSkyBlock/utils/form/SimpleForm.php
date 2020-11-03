<?php

/**
 * Copyright 2020 Fadhel
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace Fadhel\xSkyBlock\utils\form;

class SimpleForm extends Form
{
    const IMAGE_TYPE_PATH = 0;
    const IMAGE_TYPE_URL = 1;

    private $content = "";
    private $labelMap = [];

    public function __construct(?callable $callable)
    {
        parent::__construct($callable);
        $this->data["type"] = "form";
        $this->data["title"] = "";
        $this->data["content"] = $this->content;
    }

    public function processData(&$data): void
    {
        $data = $this->labelMap[$data] ?? null;
    }

    public function setTitle(string $title): void
    {
        $this->data["title"] = $title;
    }

    public function getTitle(): string
    {
        return $this->data["title"];
    }

    public function getContent(): string
    {
        return $this->data["content"];
    }

    public function setContent(string $content): void
    {
        $this->data["content"] = $content;
    }

    public function addButton(string $text, int $imageType = -1, string $imagePath = "", ?string $label = null): void
    {
        $content = ["text" => $text];
        if ($imageType !== -1) {
            $content["image"]["type"] = $imageType === 0 ? "path" : "url";
            $content["image"]["data"] = $imagePath;
        }
        $this->data["buttons"][] = $content;
        $this->labelMap[] = $label ?? count($this->labelMap);
    }
}