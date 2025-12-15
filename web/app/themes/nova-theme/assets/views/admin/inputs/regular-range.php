<?php
/**
 * Admin Theme Settings Range Input
 *
 * @author  Movve - https://movve.nl
 * @package Nova
 * @since   1.0.0
 */

if ( ! defined( 'NOVA_INIT' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

$id          = $id ?? '';
$name        = $name ?? '';
$label       = $label ?? '';
$value       = $value ?? '';
$min         = $min ?? 0;
$max         = $max ?? 100;
$step        = $step ?? 1;
$unit        = $unit ?? '';
$description = $description ?? '';
?>

<div class="nova-form-group nova-range-group">
    <label for="<?php echo esc_attr( $id ); ?>" class="nova-form-label">
		<?php echo esc_html( $label ); ?>
    </label>
	<?php if ( ! empty( $description ) ): ?>
        <p class="nova-form-description"><?php echo esc_html( $description ); ?></p>
	<?php endif; ?>
    <div class="nova-range-container">
        <input
                type="range"
                id="<?php echo esc_attr( $id ); ?>"
                name="<?php echo esc_attr( $name ); ?>"
                class="nova-form-input nova-form-range"
                min="<?php echo esc_attr( $min ); ?>"
                max="<?php echo esc_attr( $max ); ?>"
                step="<?php echo esc_attr( $step ); ?>"
                value="<?php echo esc_attr( $value ); ?>"
        />
        <span class="nova-range-value" data-unit="<?php echo esc_attr( $unit ); ?>">
            <?php echo esc_html( $value . $unit ); ?>
        </span>
    </div>
</div>

<script>
    (function() {
        const rangeInput = document.getElementById('<?php echo esc_js( $id ); ?>');
        const valueDisplay = rangeInput.nextElementSibling;
        const unit = valueDisplay.dataset.unit;

        rangeInput.addEventListener('input', function() {
            valueDisplay.textContent = this.value + unit;
        });
    })();
</script>

<style>
    .nova-range-group {
        margin-bottom: 20px;
    }

    .nova-range-container {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .nova-form-range {
        flex: 1;
        height: 4px;
        -webkit-appearance: none;
        appearance: none;
        background: #ddd;
        border-radius: 5px;
        outline: none;
    }

    .nova-form-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        background: #2271b1;
        cursor: pointer;
        border-radius: 50%;
    }

    .nova-form-range::-moz-range-thumb {
        width: 18px;
        height: 18px;
        background: #2271b1;
        cursor: pointer;
        border-radius: 50%;
        border: none;
    }

    .nova-form-range:hover::-webkit-slider-thumb {
        background: #135e96;
    }

    .nova-form-range:hover::-moz-range-thumb {
        background: #135e96;
    }

    .nova-range-value {
        min-width: 60px;
        font-weight: 600;
        color: #2271b1;
        text-align: right;
    }
</style>
