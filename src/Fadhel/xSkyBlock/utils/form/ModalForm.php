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

class ModalForm extends Form
{

    private $content = "";

    public function __construct(?callable $callable)
    {
        parent::__construct($callable);
        $this->data["type"] = "modal";
        $this->data["title"] = "";
        $this->data["content"] = $this->content;
        $this->data["button1"] = "";
        $this->data["button2"] = "";
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

    public function setButton1(string $text): void
    {
        $this->data["button1"] = $text;
    }

    public function getButton1(): string
    {
        return $this->data["button1"];
    }

    public function setButton2(string $text): void
    {
        $this->data["button2"] = $text;
    }

    public function getButton2(): string
    {
        return $this->data["button2"];
    }
}