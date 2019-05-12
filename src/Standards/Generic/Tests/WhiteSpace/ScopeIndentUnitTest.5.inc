<?php

function entity_permissions($entity_type_id, $operation) {
    switch ($entity_type_id) {
        case 'entity_test':
            switch ($operation) {
                case 'delete':
                    return ['administer entity_test content'];
            }
        case 'node':
            switch ($operation) {
                case 'view':
                    return ['access content'];
            }
    }
}
