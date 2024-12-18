<?php

/**
 * @file plugins/importexport/native/tests/functional/FunctionalNativeImportTest.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class FunctionalNativeImportTest
 * @ingroup plugins_importexport_native_tests_functional
 *
 * @brief Test native OJS import.
 */

require_mock_env('env1');

import('lib.pkp.classes.core.PKPRouter');
import('lib.pkp.tests.functional.plugins.importexport.FunctionalImportExportBaseTestCase');

class FunctionalNativeImportTest extends FunctionalImportExportBaseTestCase {
	private $expectedDois = array(
		'Issue' => '10.1234/t.v1i1-imp-test',
		'Submission' => '10.1234/t.v1i1.1-imp-test',
		'Galley' => '10.1234/t.v1i1.1.g1-imp-test',
	);
	private $expectedURNs = array(
		'Issue' => 'urn:nbn:de:0000-t.v1i1-imp-test8',
		'Submission' => 'urn:nbn:de:0000-t.v1i1.1-imp-test5',
		'Galley' => 'urn:nbn:de:0000-t.v1i1.1.g1-imp-test5',
	);

	/**
	 * @see WebTestCase::getAffectedTables()
	 */
	protected function getAffectedTables() {
		return array(
			'submissions', 'submission_files', 'submission_galleys', 'publication_galley_settings', 'submission_search_object_keywords',
			'submission_search_objects', 'submission_settings',
			'authors', 'custom_issue_orders', 'custom_section_orders', 'event_log', 'event_log_settings',
			'issue_settings', 'issues', 'sessions', 'temporary_files', 'users'
		);
	}

	/**
	 * @see WebTestCase::setUp()
	 */
	protected function setUp() : void {
		parent::setUp();
		$request = Application::get()->getRequest();
		if (is_null($request->getRouter())) {
			$router = new PKPRouter();
			$request->setRouter($router);
		}
	}

	public function testNativeDoiImport() {
		$testfile = 'tests/functional/plugins/importexport/native/testissue.xml';
		$args = array('import', $testfile, 'test', 'admin');
		$result = $this->executeCli('NativeImportExportPlugin', $args);
		self::assertRegExp('/##plugins.importexport.native.import.success.description##/', $result);

		$daos = array(
			'Issue' => 'IssueDAO',
			'Submission' => 'SubmissionDAO',
			'Galley' => 'ArticleGalleyDAO',
		);
		$articleId = null;
		foreach ($daos as $objectType => $daoName) {
			$dao = DAORegistry::getDAO($daoName);
			$pubObject = call_user_func(array($dao, "get{$objectType}ByPubId"), 'doi', $this->expectedDois[$objectType]);
			self::assertNotNull($pubObject, "Error while testing $objectType: object or DOI has not been imported.");
			$pubObjectByURN = call_user_func(array($dao, "get{$objectType}ByPubId"), 'other::urn', $this->expectedURNs[$objectType]);
			self::assertNotNull($pubObjectByURN, "Error while testing $objectType: object or URN has not been imported.");
			if ($objectType == 'Submission') {
				$articleId = $pubObject->getId();
			}
		}

		// Trying to import the same file again should lead to an error.
		$args = array('import', $testfile, 'test', 'admin');
		$result = $this->executeCli('NativeImportExportPlugin', $args);
		self::assertRegExp('/##plugins.importexport.native.import.error.duplicatePubId##/', $result);

		// Delete inserted article files from the filesystem.
		$contextId = Application::get()->getRequest()->getContext()->getId();
		$submissionDir = Services::get('submissionFile')->getSubmissionDir($contextId, $articleId);
		Services::get('file')->fs->delete($submissionDir);
	}

	public function testNativeDoiImportWithErrors() {
		$testfile = 'tests/functional/plugins/importexport/native/testissue-with-errors.xml';
		$args = array('import', $testfile, 'test', 'admin');
		$result = $this->executeCli('NativeImportExportPlugin', $args);
		self::assertRegExp('/##plugins.importexport.native.import.error.unknownPubId##/', $result);
	}
}

