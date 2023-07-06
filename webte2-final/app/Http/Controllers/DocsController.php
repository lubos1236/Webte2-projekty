<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use Parsedown;

class DocsController extends Controller
{
  public function show(Request $request, string $role)
  {
    $qParams = $request->query();
    $acceptHeader = $request->getAcceptableContentTypes();

    $lang = $qParams["lang"] ?? "en";

    $fileName = match ($role) {
      "student" => "guide_student_$lang.md",
      "teacher" => "guide_teacher_$lang.md",
      "admin" => "guide_admin_$lang.md",
      default => null,
    };

    if ($fileName === null) {
      return response("Markdown Docs For $role Not Found", 404);
    }

    if (in_array("text/markdown", $acceptHeader)) {
      return $this->getMarkdown($fileName);
    }

    if (in_array("application/pdf", $acceptHeader)) {
      return $this->getPdf($fileName);
    }

    return response('Not Acceptable', 406);
  }

  // TODO: Maybe one time next time
  // public function edit() {}

  private function getMarkdown(string $fileName)
  {
    $filePath = "docs/$fileName";

    if (!Storage::disk('local')->exists($filePath)) {
      return response('Markdown Docs Not found', 404);
    }

    $content = Storage::disk('local')->get($filePath);

    return response($content, 200, [
      'Content-Type' => 'text/markdown'
    ]);
  }

  private function getPdf(string $fileName)
  {
    $filePath = "docs/$fileName";

    if (!Storage::disk('local')->exists($filePath)) {
      return response('Pdf Docs Not found', 404);
    }

    $parsedown = new Parsedown();
    $markdown = Storage::disk('local')->get($filePath);
    $html = $parsedown->text($markdown);

    $mpdf = new Mpdf([
      'tempDir' => storage_path('tmp'),
    ]);
    $mpdf->WriteHTML($html);
    $output = $mpdf->Output('', 'S');

    return response($output, 200, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'attachment; filename="docs.pdf"'
    ]);
  }
}
