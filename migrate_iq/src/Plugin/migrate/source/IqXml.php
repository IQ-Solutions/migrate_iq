<?php
/**
 * @file
 * Contains \Drupal\migrate_iq\Plugin\migrate\source\iqxml.
 */

namespace Drupal\migrate_iq\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;

/**
 * Source for IQ XML files.
 *
 * @MigrateSource(
 *   id = "iqxml"
 * )
 */
class IqXml extends SourcePluginBase {
	
	/**
	 * {@inheritdoc}
	 */
	public function fields() {
		$fields = [];
		
		foreach($this->configuration['fields'] as $index => $val) {
			$fields[$index] = $this->t($val['label']);
		}
		
		return $fields;
	}
	
	public function initializeIterator() {
		$path = DRUPAL_ROOT.DIRECTORY_SEPARATOR.drupal_get_path('module',
				'migrate_iq').DIRECTORY_SEPARATOR;
		
		$file = simplexml_load_file($path.'..'.DIRECTORY_SEPARATOR
		                            .$this->configuration['path']);
		$fields = [];
		
		foreach($this->configuration['fields'] as $index => $val) {
			$fields[$index] = $val['selector'];
		}
				
		$nodeArray = new \ArrayIterator();
		
		foreach($file->node as $index=>$node) {
			
			if(empty((string) $node->title)) {
				continue;
			}
			else {
				$currentNode = [];
				
				foreach($fields as $key => $val) {
					if(property_exists($node,$val)) {
						$currentNode[$key] = (string) $node->{$val};
					}
				}
				
				$nodeArray->append($currentNode);
			}
		}
		
		return $nodeArray;
	}
	
	public function getIDs() {
		$ids = array();
		foreach ($this->configuration['keys'] as $key) {
			$ids[$key]['type'] = 'string';
		}
		return $ids;
	}
	
	public function __toString() {
		return (string) $this->query;
	}
}