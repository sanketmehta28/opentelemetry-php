<?php

declare(strict_types=1);

namespace OpenTelemetry\SDK\Trace\Behavior;

use OpenTelemetry\SDK\Common\Future\CancellationInterface;
use OpenTelemetry\SDK\Trace\SpanDataInterface;
use OpenTelemetry\SDK\Trace\SpanExporterInterface;

trait SpanExporterTrait
{
    private bool $running = true;

    /** @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.7.0/specification/trace/sdk.md#shutdown-2 */
    public function shutdown(?CancellationInterface $cancellation = null): bool
    {
        $this->running = false;

        return true;
    }

    /** @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.7.0/specification/trace/sdk.md#forceflush-2 */
    public function forceFlush(?CancellationInterface $cancellation = null): bool
    {
        return true;
    }

    abstract public static function fromConnectionString(string $endpointUrl, string $name, string $args);

    /**
     * @param iterable<SpanDataInterface> $spans Batch of spans to export
     *
     * @see https://github.com/open-telemetry/opentelemetry-specification/blob/v1.7.0/specification/trace/sdk.md#exportbatch
     *
     * @psalm-return SpanExporterInterface::STATUS_*
     */
    public function export(iterable $spans, ?CancellationInterface $cancellation = null): int
    {
        if (!$this->running) {
            return SpanExporterInterface::STATUS_FAILED_NOT_RETRYABLE;
        }

        return $this->doExport($spans); /** @phpstan-ignore-line */
    }

    /**
     * @param iterable<SpanDataInterface> $spans Batch of spans to export
     *
     * @psalm-return SpanExporterInterface::STATUS_*
     */
    abstract protected function doExport(iterable $spans): int; /** @phpstan-ignore-line */
}
