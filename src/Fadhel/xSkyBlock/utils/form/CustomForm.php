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

class CustomForm extends Form
{
    private $labelMap = [];

    public function __construct(?callable $callable)
    {
        parent::__construct($callable);
        $this->data["type"] = "custom_form";
        $this->data["title"] = "";
        $this->data["content"] = [];
    }

    public function processData(&$data): void
    {
        if (is_array($data)) {
            $new = [];
            foreach ($data as $i => $v) {
                $new[$this->labelMap[$i]] = $v;
            }
            $data = $new;
        }
    }

    public function setTitle(string $title): void
    {
        $this->data["title"] = $title;
    }

    public function getTitle(): string
    {
        return $this->data["title"];
    }

    public function addLabel(string $text, ?string $label = null): void
    {
        $this->addContent(["type" => "label", "text" => $text]);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function addToggle(string $text, bool $default = null, ?string $label = null): void
    {
        $content = ["type" => "toggle", "text" => $text];
        if ($default !== null) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function addSlider(string $text, int $min, int $max, int $step = -1, int $default = -1, ?string $label = null): void
    {
        $content = ["type" => "slider", "text" => $text, "min" => $min, "max" => $max];
        if ($step !== -1) {
            $content["step"] = $step;
        }
        if ($default !== -1) {
            $content["default"] = $default;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function addStepSlider(string $text, array $steps, int $defaultIndex = -1, ?string $label = null): void
    {
        $content = ["type" => "step_slider", "text" => $text, "steps" => $steps];
        if ($defaultIndex !== -1) {
            $content["default"] = $defaultIndex;
        }
        $this->addContent($content);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function addDropdown(string $text, array $options, int $default = null, ?string $label = null): void
    {
        $this->addContent(["type" => "dropdown", "text" => $text, "options" => $options, "default" => $default]);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    public function addInput(string $text, string $placeholder = "", string $default = null, ?string $label = null): void
    {
        $this->addContent(["type" => "input", "text" => $text, "placeholder" => $placeholder, "default" => $default]);
        $this->labelMap[] = $label ?? count($this->labelMap);
    }

    private function addContent(array $content): void
    {
        $this->data["content"][] = $content;
    }
}