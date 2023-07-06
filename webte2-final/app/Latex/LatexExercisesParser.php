<?php

namespace App\Latex;

class LatexExercisesParser
{
  private string $filePath;
  private string $documentPreamble;

  public function __construct(string $filePath)
  {
    $this->filePath = $filePath;
  }

  /**
   * Execute latex document parsing.
   *
   * @return array Array of parsed exercises from latex document as associative arrays
   *  with two props "task" and "solution". The content of each prop is a complete latex file.
   */
  public function parse(): array
  {
    $file = fopen($this->filePath, "r");
    $line = fgets($file);

    $this->documentPreamble = "";
    $sections = [];

    $isInsideDocumentBody = false;

    while ($line !== false) {
      if (str_starts_with(trim($line), "\\begin{document}")) {
        $isInsideDocumentBody = true;
        $this->documentPreamble .= "\n" . $line;
        $this->documentPreamble .= "%s\n";
        $line = fgets($file);
        continue;
      }

      if (str_starts_with(trim($line), "\\end{document}")) {
        $isInsideDocumentBody = false;
        $this->documentPreamble .= $line;
        break;
      }

      if ($isInsideDocumentBody) {
        $this->parseBodyContent($sections, $line);
      } else {
        $this->parsePreamble($line);
      }

      $line = fgets($file);
    }

    fclose($file);

    return $this->parseSections($sections);
  }

  private function parseBodyContent(array &$sections, string $line): void
  {
    if (str_starts_with(trim($line), "\\section")) {
      // start parse new section
      $sections[] = $line;
      return;
    }

    // ignore empty lines
    if (empty(trim($line))) {
      return;
    }

    // append to currently parsed section
    $lastIndex = empty($sections) ? 0 : count($sections) - 1;
    $sections[$lastIndex] .= $line;
  }

  private function parsePreamble(string &$line): void
  {
    $trimmedLine = trim($line);
    // ignore new environment declarations
    if (str_starts_with($trimmedLine, "\\documentclass")) {
      $this->documentPreamble .= $line;
    }
  }

  private function parseSections(&$sections): array
  {
    $parsedSections = [];

    foreach ($sections as $section) {
      $parsedSection = $this->parseSection($section);

      // embed parsed section into document preamble
      $parsedSection["task"] = sprintf($this->documentPreamble, $parsedSection["task"]);
      $parsedSection["solution"] = trim($parsedSection["solution"]);

      $parsedSections[] = $parsedSection;
    }

    return $parsedSections;
  }

  private function parseSection(string &$section): array
  {
    $parsedSection = [
      "task" => "",
      "solution" => ""
    ];

    $scope = null;

    foreach (explode("\n", $section) as $line) {
      $trimmedLine = trim($line);
      if (str_starts_with($trimmedLine, "\\begin{task}")) {
        $scope = "task";
        continue;
      } else if (str_starts_with($trimmedLine, "\\begin{solution}")) {
        $scope = "solution";
        continue;
      } else if (str_starts_with($trimmedLine, "\\end{task}") ||
        str_starts_with($trimmedLine, "\\end{solution}")) {
        $scope = null;
      } else if ((str_starts_with($trimmedLine, "\\begin{equation*}") || str_starts_with($trimmedLine, "\\end{equation*}"))) {
        if ($scope === 'task') {
          $line = str_replace("\\begin{equation*}", "$$", $line);
          $line = str_replace("\\end{equation*}", "$$", $line);
        } else {
          continue;
        }
      }

      if ($scope == null) {
        continue;
      }

      $parsedSection[$scope] .= $line;
    }

    return $parsedSection;
  }
}
