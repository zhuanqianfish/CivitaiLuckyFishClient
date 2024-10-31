<?php
class FileHelper {
    // 读取文件内容
    public static function readFile($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("File does not exist: {$filePath}");
        }
        return file_get_contents($filePath);
    }

    // 写入内容到文件
    public static function writeFile($filePath, $content) {
        $fileHandle = fopen($filePath, 'w');
        if ($fileHandle === false) {
            throw new Exception("Unable to open file: {$filePath}");
        }
        $bytesWritten = fwrite($fileHandle, $content);
        fclose($fileHandle);
        return $bytesWritten;
    }

    // 追加内容到文件
    public static function appendToFile($filePath, $content) {
        $fileHandle = fopen($filePath, 'a');
        if ($fileHandle === false) {
            throw new Exception("Unable to open file: {$filePath}");
        }
        $bytesWritten = fwrite($fileHandle, $content);
        fclose($fileHandle);
        return $bytesWritten;
    }

    // 删除文件
    public static function deleteFile($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("File does not exist: {$filePath}");
        }
        return unlink($filePath);
    }

    // 检查文件是否存在
    public static function fileExists($filePath) {
        return file_exists($filePath);
    }

    //列出目录下所有文件
    public static function listFiles($directory) {
        if (!is_dir($directory)) {
            throw new Exception("Directory does not exist: {$directory}");
        }
        $files = array_diff(scandir($directory), array('..', '.'));
        $files = array_values($files );
        return $files;
    }


    // 列出目录下所有文件，排除 . 和 .. 并将文件内容读入数组
    public static function listFilesWithContent($directory) {
        if (!is_dir($directory)) {
            throw new Exception("Directory does not exist: {$directory}");
        }
        $files = array_diff(scandir($directory), array('..', '.'));
        $fileContents = array();
        foreach ($files as $file) {
            $filePath = $directory . '/' . $file;
            if (is_file($filePath)) {
                $fileContents[] =  ['name'=>$file, 'content'=> self::readFile($filePath) ] ;
            }
        }
        return $fileContents;
    }
}

/*

// 使用示例
try {
    // 列出目录下所有文件并读取内容
    $filesContent = FileHelper::listFilesWithContent('path/to/directory');
    foreach ($filesContent as $fileName => $content) {
        echo "File: {$fileName}\nContent:\n{$content}\n";
    }
} catch (Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
}
    */