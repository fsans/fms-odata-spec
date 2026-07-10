<?php

declare(strict_types=1);

namespace FmsOData\Spec\Tests\Containers;

use FmsOData\Spec\Containers\ContainerBinaryMimeType;
use FmsOData\Spec\Containers\ContainerDownload;
use FmsOData\Spec\Containers\ContainerUploadInput;
use FmsOData\Spec\Containers\Containers;
use FmsOData\Spec\Containers\FMContainerAnnotations;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for the Containers domain, ported from test_containers.py.
 */
final class ContainersTest extends TestCase
{
    public function testContainerBinaryMimeTypesComplete(): void
    {
        $values = \array_map(
            static fn (ContainerBinaryMimeType $m): string => $m->value,
            ContainerBinaryMimeType::all(),
        );

        self::assertSame(
            ['image/png', 'image/jpeg', 'image/gif', 'image/tiff', 'application/pdf'],
            $values,
        );
    }

    #[DataProvider('sniffMimeProvider')]
    public function testSniffContainerMime(string $bytes, ?string $expected): void
    {
        $result = Containers::sniffMime($bytes);

        if ($expected === null) {
            self::assertNull($result);
        } else {
            self::assertNotNull($result);
            self::assertSame($expected, $result->value);
        }
    }

    /**
     * @return array<string, array{string, ?string}>
     */
    public static function sniffMimeProvider(): array
    {
        return [
            'png' => ["\x89PNG", 'image/png'],
            'jpeg' => ["\xff\xd8\xff\xe0", 'image/jpeg'],
            'gif' => ['GIF8', 'image/gif'],
            'tiff little endian' => ["II*\x00", 'image/tiff'],
            'tiff big endian' => ["MM\x00*", 'image/tiff'],
            'pdf' => ['%PDF', 'application/pdf'],
            'unknown xxxx' => ['XXXX', null],
            'unknown abc' => ['abc', null],
            'empty' => ['', null],
        ];
    }

    public function testBuildContentDispositionAscii(): void
    {
        self::assertSame(
            'inline; filename=file.txt',
            Containers::buildContentDisposition('file.txt'),
        );
    }

    public function testBuildContentDispositionNonAscii(): void
    {
        $value = Containers::buildContentDisposition('café.pdf');

        self::assertStringStartsWith("inline; filename*=UTF-8''", $value);
        self::assertStringContainsString('caf', $value);
    }

    public function testToBase64Roundtrip(): void
    {
        self::assertSame('hello world', \base64_decode(Containers::toBase64('hello world')));
    }

    public function testToBase64Empty(): void
    {
        self::assertSame('', Containers::toBase64(''));
    }

    public function testContainerUploadInputConstructs(): void
    {
        $input = new ContainerUploadInput("\x89PNG", 'image/png', 'x.png');

        self::assertSame("\x89PNG", $input->data);
        self::assertNull($input->encoding);
    }

    public function testContainerDownloadConstructs(): void
    {
        $download = new ContainerDownload('x', 'text/plain', 'a.txt');

        self::assertSame('x', $download->data);
        self::assertSame('a.txt', $download->filename);
    }

    public function testFmContainerAnnotationsToODataArray(): void
    {
        $annotations = new FMContainerAnnotations('x.png', 'image/png', 'b64');

        self::assertSame(
            [
                '@com.filemaker.odata.Filename' => 'x.png',
                '@com.filemaker.odata.ContentType' => 'image/png',
                'data' => 'b64',
            ],
            $annotations->toODataArray(),
        );
    }
}
